<?php
/**
 *   public function actions()
  {
    return array(
      'userSearch'=>array(
        'class'=>'ext.SRelationWidgets.actions.SAutocompleteAjaxAction',
        'model'=>'User', // Class name of model to search
        'attribute'=>'id', //The attribute of the model to search
        'limit'=>15, //The attribute of the model to display
      ),
    );
  }
 */
class SAutocompleteAjaxAction extends CAction
{
  public $model;
  public $attribute;
  public $criteria;
  public $limit = 10;

  public function run()
  {
    if(isset($this->model) && isset($this->attribute)) {
      $results = array();
      $q = $_GET['term'];
      if(is_int($q))
        $q = (int) $q;
      if (isset($q)) {
        if(is_array($this->criteria)) {
          $criteria = new CDbCriteria($this->criteria);
        } else {
          $criteria = new CDbCriteria();
        }
        $criteria->addSearchCondition("t.".$this->attribute, trim($q));
        $criteria->limit = $this->limit;
        $model = new $this->model;
        foreach($model->findAll($criteria) as $m)
        {
          $results[] = $m->{$this->attribute};
        }
      }
    }
    echo CJSON::encode($results);
    Yii::app()->end();
  }
}
