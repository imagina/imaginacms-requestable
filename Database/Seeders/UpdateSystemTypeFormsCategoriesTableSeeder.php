<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Requestable\Repositories\CategoryRepository;

class UpdateSystemTypeFormsCategoriesTableSeeder extends Seeder
{

  private $categoryRuleRepository;

  public function __construct(
    CategoryRepository $categoryRepository
  )
  {
    $this->categoryRepository = $categoryRepository;
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $params = [
      "filter" => [],
      "include" => [],
      "fields" => [],
    ];
    $categories = $this->categoryRepository->getItemsBy($params);
    foreach ($categories as $category) {
      foreach ($category->form as $form) {
        foreach ($form->fields as $field) {
          if ($field->name == 'comment' || $field->name == 'value') {
            \DB::table('iforms__fields')->where('id', $field->id)->update(['system_type' => 'requestableField-' . $field->name]);
          } elseif ($field->name == 'name' || $field->name == 'lastname' || $field->name == 'telephone' || $field->name == 'email') {
            \DB::table('iforms__fields')->where('id', $field->id)->update(['system_type' => 'requestableHiddenField-' . $field->name]);
          }
        }
      }
    }
  }
}
