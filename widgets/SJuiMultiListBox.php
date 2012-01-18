<?php
/**
 * SJuiListBox class file.
 *
 */
Yii::import('zii.widgets.jui.CJuiInputWidget');
/**
 * SJuiListBox
 *
 */
class SJuiMultiListBox extends CJuiInputWidget
{
  /**
   * @var string the input data (the entire list of models)
   */
  public $data;

  /**
   * Initializes everything
   *
   * @return void
   */
  public function init()
  {
    parent::init();
    list($name,$id)=$this->resolveNameID();
    if(isset($this->htmlOptions['id']))
      $id=$this->htmlOptions['id'];
    else
      $this->htmlOptions['id']=$id;

    $basePath=Yii::getPathOfAlias('ext.SRelationWidgets.assets');
    $baseUrl = Yii::app()->getAssetManager()->publish($basePath);

    $cs=Yii::app()->getClientScript();
    $cs->registerCssFile($baseUrl . '/' . 'ui.multiselect.css');

    $this->scriptUrl=$baseUrl;
    $this->registerScriptFile('ui.multiselect.js');

    $options = CJavaScript::encode($this->options);

    $cs->registerScript(
      'sjuilistbox-'.$id,
      '$("#'.$id.'").multiselect('.$options.');',
      CClientScript::POS_READY
    );
  }

  /**
   * Run this widget.
   * This method renders the needed HTML code.
   */
  public function run()
  {
    if(isset($this->htmlOptions['name']))
      $name=$this->htmlOptions['name'];
    else
      $name = $this->name;

    if(isset($this->data[''])) // remove the "blank" option, if there is one, since you can unselect it
      unset($this->data['']);

    if($this->hasModel())
      echo CHtml::ActiveListBox(
        $this->model,
        $this->attribute,
        $this->value,
        $this->htmlOptions);
    else
      echo CHtml::ListBox(
        $name,
        $this->value, // keys to select
        $this->data, // data
        $this->htmlOptions);
  }

}
