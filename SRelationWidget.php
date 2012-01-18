<?php

// todo handle composite primary keys?

class SRelationWidget extends CWidget
{
  const DATA_FORMAT_ARRAY = 'array';
  const DATA_FORMAT_TEXT = 'text';

  /**
   * @var CModel $model the model this widget will be presenting
   */
  public $model;

  /**
   * @var string $relation the relation name to the FK table
   */
  public $relation;

  /**
	 * @var string the input name override - will be set automatically if blank
	 */
	public $name;

  /**
   * @var string $relationDisplayField the field to display of the related model
   */
  public $relationDisplayField;

  /**
   * @var string $relationOptionCriteria optional criteria to limit the option choices
   */
  public $relationOptionCriteria;

  /**
   * @var bool $allowEmpty insert and empty option in the widget or not
   */
  public $allowEmpty = true;

  /**
   * @var string $widgetClassName class name (can be in path alias format)
   */
  public $widgetClassName;

  /**
   * @var array $widgetProperties initial property values for the widget
   */
  public $widgetProperties = array();

  /**
   * @var array $widgetDataFormat the format of the data
   * default: self::DATA_FORMAT_ARRAY
   */
  public $widgetDataFormat = self::DATA_FORMAT_ARRAY;

  /**
   * @var array $widgetDataFormat the delimiter if using text format
   * default
   */
  public $widgetDataDelimiter = ',';

  /**
   * @var string $_modelAttribute if foreign key field of this relation
   */
  private $_modelAttribute;

  /**
   * @var string $_relationTable the MANY_MANY relation table
   */
  //private $_relationTable;

  /**
   * @var string $_relatedModel the related model's class name
   */
  private $_relatedModel;

  /**
   * @var string $_relatedModelPk the related model's primary key name
   */
  private $_relatedModelPk;

  /**
   * @var string $_relationType the type of relation i.e. MANY_MANY, HAS_MANY, BELONGS_TO, HAS_ONE
   */
  private $_relationType;

  /**
   * Initializes the widget.
   * This method is called by {@link CBaseController::createWidget}
   * and {@link CBaseController::beginWidget} after the widget's
   * properties have been initialized.
   */
  public function init()
  {
    // check that the necessary info has been passed in to the widget
    if (!$this->hasModelRelation())
      throw new CException(Yii::t('yii','{class} must specify "model" and "relation" values.',array('{class}'=>get_class($this))));

    // set the name if there is an override
    //$this->resolveName();

    // Sort out the type of relation, and set it up accordingly
    $relations = $this->model->relations();
    // $key = Name of the Relation
    // $value[0] = Type of the Relation
    // $value[1] = Related Model
    // $value[2] = Related Field or Many_Many Table
    if($details = $relations[$this->relation]) {
      $this->_relationType = $details[0]; // save the type of relation
      $this->_relatedModel = new $details[1]; // set up an model of the related kind
      $this->_relatedModelPk = $this->_relatedModel->tableSchema->primaryKey; // get the related model's primay key
      switch($this->_relationType)
      {
        case 'CBelongsToRelation':
          $this->setupBelongsTo($details);
          break;
        case 'CHasOneRelation':
          throw new CException('CHasOneRelation not supported by SRelationWidget yet');
          // todo handle HasOne relations
          //$this->setupHasOne($details);
          break;
        case 'CManyManyRelation':
          $this->setupManyMany($details);
          break;
        case 'CHasManyRelation':
          throw new CException('CHasManyRelation not supported by SRelationWidget yet');
          // todo handle HasMany relations
          //$this->setupHasMany($details);
          break;
        default:
          throw new CException('Unknown relation type for SRelationWidget');
      }
    }
  }

  /**
   * @return boolean whether this widget is associated with a data model and relation.
   */
  protected function hasModelRelation()
  {
    return $this->model instanceof CModel && $this->relation !== null;
  }

  /**
   * This basically syncs name overrides - if this widget has a Name set, it's set on the
   * @return null|string
   */
  protected function resolveWidgetName()
  {
    // if there is no widget name, set it to this name
    if($this->name !== null && !isset($this->widgetProperties['name'])) { // if the name is set but no the widget name
      $this->widgetProperties['name'] = $this->name; // set this name on the widget
      return $this->widgetProperties['name'];
    }
    return null;
  }

  protected function resolveMultiWidgetName()
  {
    if(!$this->resolveWidgetName()) { // if no name
      $this->widgetProperties['name'] = get_class($this->model).'['.$this->relation.']'; // set this as the widget name
      return $this->widgetProperties['name'];
    }
    return null;
    //return $this->name;
  }

  protected function resolveWidgetId()
  {
    // if there is no widget id, set it to this id
    if($this->getId(false) !== null && !isset($this->widgetProperties['id'])) { // if the name is set but no the widget name
      $this->widgetProperties['id'] = $this->getId(false); // set this name on the widget
      return $this->widgetProperties['id'];
    }
    return null;
  }

  protected function setupManyMany($relationDetails) {
    //preg_match_all('/^.*\(/', $relationDetails[2], $matches);
    //$this->_relationTable = substr($matches[0][0], 0, strlen($matches[0][0]) -1);

    // set up the name and ID for the widget
    $this->resolveMultiWidgetName(); // sets the right name for the multi widget
    $this->resolveWidgetId(); // sets the right id for the multi widget

    // attributes to pass to widget
    $widgetAttrs = array();

    // determine data type
    if ($this->widgetDataFormat == self::DATA_FORMAT_TEXT) {
      $widgetAttrs['value'] = implode($this->widgetDataDelimiter.' ',$this->getRelatedListData());
    } else { // the default is self::DATA_FORMAT_ARRAY
      $widgetAttrs['value'] =	array_keys($this->getRelatedListData()); // the keys of the data already selected
      $widgetAttrs['data'] =	$this->getAllRelatedListData(); // all of the options to select from
      $widgetAttrs['htmlOptions'] = array(
        'multiple' => 'multiple' // make sure whatever CHtml input widget it is is a multi select!
      );
    }

    // merge the widget properties
    $this->widgetProperties = CMap::mergeArray($this->widgetProperties,$widgetAttrs);
  }

  protected function setupBelongsTo($relationDetails) {
    // get and verify the relation foreign key field
    if (isset($relationDetails[2]) && $relationDetails[2]) {
      $this->_modelAttribute = $relationDetails[2];
    } else {
      throw new CException(Yii::t('yii','The {relation} relation in {model} does not specify a foreign key field for the {class} widget.',
        array(
          '{relation}'=>$this->relation,
          '{model}'=>get_class($this->model),
          '{class}'=>get_class($this),
        )));
    }

    // see if empty is allowed
    if($this->model->isAttributeRequired($this->_modelAttribute)) {
      $this->allowEmpty = false;
    }

    // set up the name and ID for the widget
    $this->resolveWidgetName(); // sets the right name for the multi widget
    $this->resolveWidgetId(); // sets the right id for the multi widget

    // attributes to pass to widget
    $widgetAttrs = array();

    // set default values based on data type
    if ($this->widgetDataFormat == self::DATA_FORMAT_TEXT) {
      $widgetAttrs['htmlOptions'] = array(
        'value' => $this->getRelatedData()->{$this->relationDisplayField}
      );
    } else { // self::DATA_FORMAT_ARRAY
      $widgetAttrs['value'] = $this->getAllRelatedListData();
    }

    // set up variables for the widget
    $widgetAttrs['model'] = $this->model;
    $widgetAttrs['attribute'] = $this->_modelAttribute;

    // merge the widget properties
    $this->widgetProperties = CMap::mergeArray($this->widgetProperties,$widgetAttrs);
  }

  public function getRelatedData() {
    return $this->model->{$this->relation}; // get the related record(s)
  }

  public function getRelatedListData() {
    return CHtml::listData($this->getRelatedData(),$this->_relatedModelPk,$this->relationDisplayField); // get the related record
  }

  public function getAllRelatedListData() {
    $allRelatedModels = array();

    if ($this->allowEmpty)
      $allRelatedModels[''] = '-- Please Select --';

    if (is_array($this->relationOptionCriteria))
      $relatedModels = call_user_func(array($this->_relatedModel, 'model'))->findAll($this->relationOptionCriteria);
    else
      $relatedModels = call_user_func(array($this->_relatedModel, 'model'))->findAll();

    if ($relatedModels) {
      $data = CHtml::listData($relatedModels,$this->_relatedModelPk,$this->relationDisplayField);
      $allRelatedModels = array_merge($allRelatedModels,$data);
    }

    return $allRelatedModels;
  }

  /**
   * Executes the widget.
   * This method is called by {@link CBaseController::endWidget}.
   */
  public function run()
  {
    // call the widget if one is declared
    if($this->widgetClassName) {
      Yii::import('ext.SRelationWidgets.widgets.*');
      $this->widget($this->widgetClassName, $this->widgetProperties);
    } else {
      // todo have some default options like "listbox", etc, if no widget is defined
    }
  }

  /*public function getMultiple() {
    if($this->_relationType == ('CManyManyRelation')) {
      return true;
    } else {
      return false;
    }
  }


  public function getCurrentRelationsText() {
    $related = $this->getRelated(); // get the related record
    if (!$this->multiple && !is_array($related)) {
      if (!empty($this->model->{$this->attribute})) {
      return $related->{$this->displayAttr};
      }
    } elseif($this->multiple && is_array($related)) {
      return $this->concatString($related,$this->displayAttr);
    }
    return '';
  }

  public function getCurrentRelationsValue() {
    $related = $this->getRelated(); // get the related record
    if (!$this->multiple && !is_array($related)) {
      if (!empty($this->model->{$this->attribute})) {
      return $related->{$this->displayAttr};
      }
    } elseif($this->multiple && is_array($related)) {
      return $this->concatString($related,$this->_relatedModelPk);
    }
    return '';
  }

  private function concatString($modelArray, $attr) {
    $return = '';
    $count = count($modelArray);
    foreach($modelArray as $key=>$relatedModel) {
      $return .= $relatedModel->{$attr};
      $test = $key + 1;
      if ($test != $count)
        $return .= $this->splitter." ";
    }
    return $return;
  }*/

}
