A Yii form widget which renders relational data in "Display Widgets" to allow creating and updating model relations

This project is intended to be a more extensible successor to the Yii [Relation](http://www.yiiframework.com/extension/relation/) widget.

Right now only BELONGS_TO and MANY_MANY relations are supported. 

Example SRelationWidget call:

    <?php $this->widget('ext.yii-srelationwidgets.SRelationWidget',array(
      'model' => $model, // the current model
      'relation' => 'relationName', // name of the relation
      'relationDisplayField' => 'field_id', // name of column / property in the related model to display
      'widgetClassName' =>'SDropdown', // name of the widget to actually output (i.e. zii.widgets.jui.CJuiAutocomplete)
      // 'widgetDataFormat'=>'text', // optional, defaults to array
      'widgetProperties' => array( // the properties you would normally pass into the display widget
        'htmlOptions' => array(
          'class'=>'myclass',
        ),
      ),
    )); ?>

Included display widgets:
* SDropdown
* SListbox
* SJuiAutocomplete
* SJuiMultiAutocomplete
* SJuiMultiListBox

Included are some helper Actions and Behaviors to use the AJAX autocomplete widgets, which I will document at a later date.

Version: pre-beta :)
Author: Evan Johnson
http://splashlabsocial.com

Last updated: 1.18.2012