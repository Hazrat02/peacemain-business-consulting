<?php

namespace App\Support;

class SiteContentDefaults
{
    public static function banners(): array
    {
        return [
            [
                'title' => "Unlocking Your Business's Potential With Innovate Consulting.",
                'subtitle' => 'Home Banner',
                'description' => 'The primary goal of business consulting is to help organizations improve their performance, solve specific problems, and achieve their strategic objectives.',
                'button_text' => 'Contact Us',
                'button_url' => '/contact',
                'image_url' => 'https://images.unsplash.com/photo-1460472178825-e5240623afd5?auto=format&fit=crop&w=1600&q=80',
                'position' => 'Home Top',
                'status' => 'Active',
                'sort_order' => 1,
            ],
            [
                'title' => "Unlocking Your Business's Potential With Innovate Consulting.",
                'subtitle' => 'Home Banner',
                'description' => 'The primary goal of business consulting is to help organizations improve their performance, solve specific problems, and achieve their strategic objectives.',
                'button_text' => 'Contact Us',
                'button_url' => '/contact',
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1600&q=80',
                'position' => 'Home Top',
                'status' => 'Active',
                'sort_order' => 2,
            ],
            [
                'title' => "Unlocking Your Business's Potential With Innovate Consulting.",
                'subtitle' => 'Home Banner',
                'description' => 'The primary goal of business consulting is to help organizations improve their performance, solve specific problems, and achieve their strategic objectives.',
                'button_text' => 'Contact Us',
                'button_url' => '/contact',
                'image_url' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1600&q=80',
                'position' => 'Home Top',
                'status' => 'Active',
                'sort_order' => 3,
            ],
            [
                'title' => "Unlocking Your Business's Potential With Innovate Consulting.",
                'subtitle' => 'Home Banner',
                'description' => 'The primary goal of business consulting is to help organizations improve their performance, solve specific problems, and achieve their strategic objectives.',
                'button_text' => 'Contact Us',
                'button_url' => '/contact',
                'image_url' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1600&q=80',
                'position' => 'Home Top',
                'status' => 'Active',
                'sort_order' => 4,
            ],
        ];
    }

    public static function sidebarLinks(): array
    {
        return [
            ['label' => 'Home', 'url' => '/', 'status' => 'Active', 'sort_order' => 1],
            ['label' => 'Services', 'url' => '/servics', 'status' => 'Active', 'sort_order' => 2],
            ['label' => 'About Us', 'url' => '/about', 'status' => 'Active', 'sort_order' => 3],
            ['label' => 'Contact Us', 'url' => '/contact', 'status' => 'Active', 'sort_order' => 4],
            ['label' => 'FAQs', 'url' => '/faq', 'status' => 'Active', 'sort_order' => 5],
        ];
    }

    public static function faqs(): array
    {
        $item = [
            'question' => 'What is health and care consulting?',
            'answer' => 'Health and care consulting is a specialized service that provides advisory and support to healthcare organizations and stakeholders to improve operations, patient care.',
            'category' => 'General',
            'status' => 'Published',
        ];

        return [
            $item,
            $item,
            $item,
            $item,
            $item,
            $item,
        ];
    }

    public static function contactInfo(): array
    {
        return [
            'phone' => '+91-8123781857',
            'email' => 'info@peacemain.com',
            'address' => 'PEACEMAIN Ltd.,Celestia Kyriakou Matsi 5, Limassol 4529, Cyprus',
            'map_url' => 'https://maps.app.goo.gl/sDLkkAWRX1Ho9hUD9',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d10584.26445645795!2d77.58083569448927!3d12.906775654837578!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1513448609bf%3A0x22d8538479cabefa!2s250%2C%2024th%20Main%20Rd%2C%20KR%20Layout%2C%20JP%20Nagar%20Phase%206%2C%20J.%20P.%20Nagar%2C%20Bengaluru%2C%20Karnataka%20560078%2C%20India!5e0!3m2!1sen!2sbd!4v1731841896879!5m2!1sen!2sbd',
            'welcome_subject' => 'Welcome to PEACEMAIN',
            'welcome_message' => 'Thanks for contacting us. Our team will get back to you shortly.',
            'locations' => [
                [
                    'name' => 'Limassol, Cyprus',
                    'map_url' => 'https://maps.app.goo.gl/sDLkkAWRX1Ho9hUD9',
                    'phone' => '+91-8123781857',
                    'email' => 'info@peacemain.com',
                    'address' => 'PEACEMAIN Ltd.,Celestia Kyriakou Matsi 5, Limassol 4529, Cyprus',
                ],
                [
                    'name' => 'Bangalore, India',
                    'map_url' => 'https://maps.app.goo.gl/nPuSUvb5bbtuYK8W6',
                    'phone' => '+91-8123781857',
                    'email' => 'info@peacemain.com',
                    'address' => 'PEACEMAIN Consulting Pvt. Ltd., 249, 24th Main Rd, KR Layout, JP Nagar Phase 6, J. P. Nagar, Bengaluru, Karnataka 560078, India',
                ],
            ],
        ];
    }

    public static function smtpSettings(): array
    {
        return [
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_email' => 'info@peacemain.com',
            'from_name' => 'PEACEMAIN',
            'mail_template_html' => '<div style="font-family:Arial,sans-serif;padding:24px"><h2>Welcome {{name}}</h2><p>{{welcome_message}}</p><p>Regards,<br>{{from_name}}</p></div>',
        ];
    }

    public static function generalSettings(): array
    {
        return [
            'site_name' => 'PeaceMain',
            'timezone' => 'America/Los_Angeles',
            'default_country' => 'India',
            'support_email' => 'support@peacemain.com',
            'logo_url' => '',
        ];
    }
}
