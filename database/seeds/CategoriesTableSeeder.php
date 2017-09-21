<?php

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Webpatser\Uuid\Uuid;

class CategoriesTableSeeder extends Seeder
{
    private $categories = [
        'Food & Drinks' => [
            'Food'   => [
                'Healthy food',
                'Meal food',
                'Vegetarian cuisine',
                'Italian cuisine',
                'Indian cuisine',
                'Mexican cuisine',
                'BBQ Grill',
            ],
            'Drinks' => [
                'Alcoholic',
                'Non alcoholic',
            ]
        ],
        'Beauty & Fitness',
        'Retail & Services',
        'Attractions & Leisure',
        'Other & Online'
    ];

    /**
     * @var DatabaseManager
     */
    private $db;

    public function __construct(DatabaseManager $database)
    {
        $this->db = $database;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = $this->getCategories();

        $connection = $this->db->connection();

        foreach ($categories as $category) {
            if ($connection->table('categories')->where('name', $category['name'])->exists()) {
                printf('Category %s already exists. Skipping...'.PHP_EOL, $category['name']);
                continue;
            }

            $connection->table('categories')->insert($category);
        }
    }

    private function getCategories(array $categories = null, string $parent = null): iterable
    {
        if (null === $categories) {
            $categories = $this->categories;
        }

        $lastCategoryId = null;
        foreach ($categories as $category => $children) {
            $lastCategoryId = Uuid::generate(4)->__toString();

            $categoryArray = [
                'id'         => $lastCategoryId,
                'name'       => $category,
                'created_at' => Carbon::now()->__toString(),
                'updated_at' => Carbon::now()->__toString()
            ];

            if (null !== $parent) {
                $categoryArray['parent_id'] = $parent;
            }

            if (is_array($children)) {
                yield $categoryArray;

                foreach ($this->getCategories($children, $lastCategoryId) as $item) {
                    yield $item;
                }
                continue;
            }

            yield array_merge($categoryArray, ['name' => $children]);
        }
    }
}
