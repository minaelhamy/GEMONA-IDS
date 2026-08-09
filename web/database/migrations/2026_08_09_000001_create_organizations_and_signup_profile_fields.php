<?php

use App\Enums\Ask;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('type')->default('organization');
            $table->unsignedTinyInteger('status')->default(Status::ACTIVE);
            $table->unsignedTinyInteger('is_seeded')->default(Ask::NO);
            $table->timestamps();

            $table->index(['country', 'status']);
            $table->unique(['name', 'country']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('country_code')->constrained('organizations')->nullOnDelete();
            $table->string('signup_country')->nullable()->after('organization_id');
            $table->text('signup_address')->nullable()->after('signup_country');
        });

        $now = now();
        $countries = [
            'Albania', 'Algeria', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria',
            'Azerbaijan', 'Bahrain', 'Bangladesh', 'Belarus', 'Belgium', 'Bosnia and Herzegovina',
            'Brazil', 'Bulgaria', 'Burundi', 'Cameroon', 'Canada', 'Chad', 'Chile', 'China',
            'Colombia', 'Congo', 'Cote d Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic',
            'Denmark', 'Djibouti', 'Ecuador', 'Equatorial Guinea', 'Eritrea', 'Ethiopia',
            'Finland', 'France', 'Gabon', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Guinea',
            'Hungary', 'India', 'Indonesia', 'Iraq', 'Ireland', 'Italy', 'Japan', 'Jordan',
            'Kazakhstan', 'Kenya', 'Kuwait', 'Lebanon', 'Libya', 'Malaysia', 'Mali', 'Malta',
            'Mauritania', 'Mexico', 'Morocco', 'Mozambique', 'Namibia', 'Netherlands', 'New Zealand',
            'Niger', 'Nigeria', 'Norway', 'Oman', 'Pakistan', 'Palestine', 'Panama', 'Peru',
            'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda',
            'Saudi Arabia', 'Senegal', 'Serbia', 'Singapore', 'Somalia', 'South Africa',
            'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Sweden', 'Switzerland',
            'Syria', 'Tanzania', 'Thailand', 'Tunisia', 'Turkiye', 'Uganda', 'Ukraine',
            'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan',
            'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
        ];

        $organizations = array_map(function (string $country) use ($now) {
            return [
                'name'       => 'Embassy of Egypt in ' . $country,
                'country'    => $country,
                'city'       => null,
                'address'    => null,
                'type'       => 'egyptian_embassy',
                'status'     => Status::ACTIVE,
                'is_seeded'  => Ask::YES,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $countries);

        DB::table('organizations')->insert($organizations);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn(['signup_country', 'signup_address']);
        });

        Schema::dropIfExists('organizations');
    }
};
