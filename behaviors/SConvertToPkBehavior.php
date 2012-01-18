<?php
/**
 * CAdvancedArBehavior class file.
 *
 * @author
 * @link
 * @version 0.3
 */

/**
 * public function behaviors()
  {
    return array(
      'SConvertToPkBehavior'=>array(
        'class'=>'ext.SMultiAutocomplete.SConvertToPkBehavior'
      ),
    );
  }
 */

class SConvertToPkBehavior extends CBehavior
{
  public function convertToPkArray($postValue,$modelName,$attribute,$pk,$delimiter) {
    $pks = array();
    if($values = $postValue) {
      $values = explode($delimiter,$values);
      foreach($values as $value) {
        if($value) {
          if($model = call_user_func(array($modelName, 'model'))->findByAttributes(array($attribute=>trim($value))))
            $pks[] = $model->{$pk};
        }
      }
    }
    return $pks;
  }

  public function convertToPk($postValue,$modelName,$attribute,$pk) {
    $return = '';
    if($value = $postValue) {
      if($model = call_user_func(array($modelName, 'model'))->findByAttributes(array($attribute=>trim($value))))
        $return = $model->{$pk};
    }
    return $return;
  }

}
