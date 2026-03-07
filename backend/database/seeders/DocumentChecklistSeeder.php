<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentSection;
use Illuminate\Database\Seeder;

class DocumentChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $identity = DocumentSection::query()->updateOrCreate(
            ['name' => 'Identity Documents'],
            ['sort_order' => 1, 'is_active' => true]
        );

        $financial = DocumentSection::query()->updateOrCreate(
            ['name' => 'Financial Documents'],
            ['sort_order' => 2, 'is_active' => true]
        );

        $travel = DocumentSection::query()->updateOrCreate(
            ['name' => 'Travel Documents'],
            ['sort_order' => 3, 'is_active' => true]
        );

        $passport = Document::query()->updateOrCreate(
            ['slug' => 'passport-copy-default'],
            [
                'document_section_id' => $identity->id,
                'title' => 'Passport Copy',
                'description' => 'Clear scanned copy of passport bio page',
                'is_required' => true,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $bank = Document::query()->updateOrCreate(
            ['slug' => 'bank-statement-default'],
            [
                'document_section_id' => $financial->id,
                'title' => 'Bank Statement (Last 6 Months)',
                'description' => 'Signed and stamped statement preferred',
                'is_required' => true,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $marriage = Document::query()->updateOrCreate(
            ['slug' => 'marriage-certificate-default'],
            [
                'document_section_id' => $identity->id,
                'title' => 'Marriage Certificate',
                'description' => 'Required for married applicants',
                'is_required' => true,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $australiaVisa = Document::query()->updateOrCreate(
            ['slug' => 'australia-visa-form-default'],
            [
                'document_section_id' => $travel->id,
                'title' => 'Australia Visa Application Form',
                'description' => 'Applicable for Australia destination',
                'is_required' => true,
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        $passport->rules()->delete();
        $bank->rules()->delete();
        $marriage->rules()->delete();
        $australiaVisa->rules()->delete();

        $marriage->rules()->create([
            'marital_status' => 'married',
            'is_exclusion' => false,
        ]);

        $australiaVisa->rules()->create([
            'destination_country' => 'Australia',
            'is_exclusion' => false,
        ]);
    }
}

