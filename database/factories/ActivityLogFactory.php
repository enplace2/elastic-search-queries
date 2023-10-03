<?php
namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        // Random User ID between 1 and 98000
        $userId = $this->faker->numberBetween(1, 98000);

        // Random Activity Type ID between 1 and 10
        $activityTypeId = $this->faker->numberBetween(1, 10);

        // Defining model types and their id ranges
        $modelTypesWithIdRanges = [
            User::class => [1, 98000],
            File::class => [1, 1000000],
            Address::class => [1, 98000]
        ];

        $randomModelType = $this->faker->randomElement(array_keys($modelTypesWithIdRanges));
        $randomModelIdRange = $modelTypesWithIdRanges[$randomModelType];
        $randomModelId = $this->faker->numberBetween($randomModelIdRange[0], $randomModelIdRange[1]);

        return [
            'performed_by_user_id' => $userId,
            'activity_type_id' => $activityTypeId,
            'model_type' => $randomModelType,
            'model_id' => $randomModelId,
            'properties' => json_encode([
                'key1' => $this->faker->word,
                'key2' => $this->faker->randomNumber(),
                'key3' => $this->faker->boolean,
                'key4' => [
                    'sub_key1' => $this->faker->sentence,
                    'sub_key2' => $this->faker->dateTimeThisYear->format('Y-m-d H:i:s')
                ]
            ])
        ];
    }
}
