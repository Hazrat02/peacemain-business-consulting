<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentRule;
use App\Models\User;
use App\Models\UserRequiredDocument;
use Illuminate\Support\Collection;

class DocumentChecklistService
{
    public const STATUS_MISSING = 'missing';
    public const STATUS_UPLOADED = 'uploaded';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WAIVED = 'waived';

    /**
     * @return array{required:int,approved:int,waived:int,pending:int,rejected:int,missing:int,completion_percentage:float}
     */
    public function syncAndGetProgress(User $user): array
    {
        $this->syncApplicableDocuments($user);

        $requirements = UserRequiredDocument::query()
            ->where('user_id', $user->id)
            ->where('is_required', true)
            ->get(['status']);

        $totalRequired = $requirements->count();
        $approved = $requirements->where('status', self::STATUS_APPROVED)->count();
        $waived = $requirements->where('status', self::STATUS_WAIVED)->count();
        $rejected = $requirements->where('status', self::STATUS_REJECTED)->count();
        $missing = $requirements->where('status', self::STATUS_MISSING)->count();
        $pending = $requirements->whereIn('status', [self::STATUS_UPLOADED, self::STATUS_UNDER_REVIEW])->count();

        $completion = $totalRequired > 0
            ? round((($approved + $waived) / $totalRequired) * 100, 2)
            : 0.0;

        return [
            'required' => $totalRequired,
            'approved' => $approved,
            'waived' => $waived,
            'pending' => $pending,
            'rejected' => $rejected,
            'missing' => $missing,
            'completion_percentage' => $completion,
        ];
    }

    public function syncApplicableDocuments(User $user): void
    {
        $documents = Document::query()
            ->where('is_active', true)
            ->with('rules')
            ->get();

        $applicableDocumentIds = [];

        foreach ($documents as $document) {
            if ($this->isDocumentApplicable($user, $document->rules)) {
                $applicableDocumentIds[] = $document->id;
                UserRequiredDocument::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'document_id' => $document->id,
                    ],
                    [
                        'is_required' => $document->is_required,
                    ]
                );
            }
        }

        UserRequiredDocument::query()
            ->where('user_id', $user->id)
            ->whereNotIn('document_id', $applicableDocumentIds)
            ->delete();
    }

    /**
     * @return Collection<int, \App\Models\UserRequiredDocument>
     */
    public function userChecklist(User $user): Collection
    {
        $this->syncApplicableDocuments($user);

        return UserRequiredDocument::query()
            ->where('user_id', $user->id)
            ->with([
                'document.section',
                'latestSubmission',
                'submissions' => fn ($query) => $query->limit(5),
            ])
            ->get();
    }

    /**
     * @param  Collection<int, DocumentRule>  $rules
     */
    private function isDocumentApplicable(User $user, Collection $rules): bool
    {
        if ($rules->isEmpty()) {
            return true;
        }

        $includeRules = $rules->where('is_exclusion', false);
        $excludeRules = $rules->where('is_exclusion', true);

        $isIncluded = $includeRules->isEmpty()
            ? true
            : $includeRules->contains(fn (DocumentRule $rule) => $this->matchesRule($user, $rule));

        if (! $isIncluded) {
            return false;
        }

        $isExcluded = $excludeRules->contains(fn (DocumentRule $rule) => $this->matchesRule($user, $rule));

        return ! $isExcluded;
    }

    private function matchesRule(User $user, DocumentRule $rule): bool
    {
        return $this->matchesValue($rule->source_country, $user->country)
            && $this->matchesValue($rule->destination_country, $user->destination_country)
            && $this->matchesValue($rule->marital_status, $user->marital_status)
            && $this->matchesValue($rule->passport_type, $user->passport_type);
    }

    private function matchesValue(?string $ruleValue, ?string $userValue): bool
    {
        if ($ruleValue === null || $ruleValue === '') {
            return true;
        }

        if ($userValue === null || $userValue === '') {
            return false;
        }

        return mb_strtolower(trim($ruleValue)) === mb_strtolower(trim($userValue));
    }
}

