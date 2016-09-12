<?php

class Application_Form_EditRecipe extends Zend_Form {
		
	private $_recipeid;
	private $_action;
		
	public function __construct(array $params = array()) {
		$this->_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		if ($this->_action == "edit") {
			$this->_recipeid = $params['recipeid'];
		}
		parent::__construct();
	}
	
	public function init() {
	
		//Name of the form
		if ($this->_action == "edit") {
			$this->setName('editrecipe')
				 ->setAction('/food/edit/id/'.$this->_recipeid)
				 ->setMethod('post');
		} else {
			$this->setName('add')
				 ->setAction('/food/add')
				 ->setMethod('post');
		}
		
		//Title
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title:')
				->setRequired(true) 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setOrder(1)
				->setAttrib('size', '100')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		//Gallery IDs
		$gallery_id = $this->addElement(
									'hidden',
									'dummy1',
									array(
										'order' => 2,
										'required' => false,
										'ignore' => true,
										'autoInsertNotEmptyValidator' => false,
										'decorators' => array(
											array(
												'HtmlTag', array(
													'tag'  	=> 'div',
													'class'	=> 'gallery_id'
												)
											)
										)
									)
								);
		$gallery_id->dummy1->clearValidators();
				
		//Photo gallery
		$gallery = $this->addElement(
									'hidden',
									'dummy2',
									array(
										'order' => 3,
										'required' => false,
										'ignore' => true,
										'autoInsertNotEmptyValidator' => false,
										'decorators' => array(
											array(
												'HtmlTag', array(
													'tag'  	=> 'div',
													'class'	=> 'gallery',
													'id' => 'gallery'
												)
											)
										)
									)
								);
		$gallery->dummy2->clearValidators();
		
		//Photo uploads
		$file = new Zend_Form_Element_File('file');
        $file->setLabel('File')
            ->setDestination(getcwd().'/media/uploads')
			->setOrder(4)
			->setIsArray(true)
			->setDecorators(array('File'))
			->setAttribs(array('style' => 'margin-bottom:20px;','onchange' => 'readImageURL(this);'));
				
		//Ingredient ID ie. sort order
		$ingredient_id = $this->addElement('hidden', 
											'ingredient_sort_id', 
											array(	'value' => 1,
													'order' => 6,
													'decorators' => array(
														'ViewHelper'
													)
												)	
											);
			  
		//Ingredient Amount
		$ingredient_amount = new Zend_Form_Element_Text('ingredient_amount');
		$ingredient_amount->setLabel('Ingredient:')
							->setOrder(7)
							->setIsArray(true)
							->removeDecorator('HtmlTag')
							->setAttribs(array('placeholder' => 'Amount','size' => '10'));
							
		//Ingredient Unit
		$ingredient_unit = new Zend_Form_Element_Text('ingredient_unit');
		$ingredient_unit->setOrder(8)
						->setIsArray(true)
						->setDecorators(array('ViewHelper'))
						->setAttribs(array('placeholder' => 'Unit','size' => '10'));
				
		//Ingredient
		$ingredient = new Zend_Form_Element_Text('ingredient');
		$ingredient->setRequired(true) 
					->addValidator('NotEmpty')
					->setOrder(9)
					->setIsArray(true)
					->setDecorators(array('ViewHelper',array('HtmlTag',array('tag' => 'br', 'placement' => 'append', 'selfClosing' => true))))
					->setAttribs(array('placeholder' => 'Ingredient','size' => '40'));
					
		
		//Ingredient Add button			
		$ingredient_add = $this->addElement('button', 
											'addIngredientElement', 
											array(	'label' => 'Add',
													'order' => 100,
													'decorators' => array(
														array('ViewHelper'),
													)
											));
		//Ingredient Remove button	
		$ingredient_remove = $this->addElement(	'button', 
												'removeIngredientElement', 
												array(	'label' => 'Remove',
														'order' => 101,
														'decorators' => array(
															array('ViewHelper'),
															array('HtmlTag', array('tag' => 'br', 'placement' => 'append', 'selfClosing' => true)),
														)
												));
		
		//Step ID ie. sort order
		$step_id = $this->addElement('hidden', 'step_sort_id', array('value' => 1,'order' => 200,'decorators' => array('ViewHelper')));
		
		//Step
		$step = new Zend_Form_Element_Textarea('step');
		$step->setLabel('Step:')
				->setRequired(true)  
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setOrder(201)
				->setIsArray(true)
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		//Step add button
		$step_add = $this->addElement(	'button', 
										'addStepElement', 
										array(	'label' => 'Add',
												'order' => 300,
												'style' => 'margin-left:-40px;margin-top:-21px;float:left;'
										));

		//Step remove button
		$step_remove = $this->addElement('button', 
										'removeStepElement', 
										array(	'label' => 'Remove',
												'order' => 301,
												'style' => 'margin-left:4px;margin-top:-21px;float:left;',
												'decorators' => array(
													array('ViewHelper'),
													array('HtmlTag', array('tag' => 'br', 'placement' => 'append', 'selfClosing' => true)),
												)
										));
		
		//Tag label
		$tag_label = new Application_Form_Element_Note('tag_label');
		$tag_label->setValue('Tags:')
					->setOrder(400)
					->setDecorators(array('ViewHelper'));
		
		//Tag list
		$tags = $this->addElement(
									'hidden',
									'dummy3',
									array(
										'order' => 401,
										'required' => false,
										'ignore' => true,
										'autoInsertNotEmptyValidator' => false,
										'decorators' => array(
											array(
												'HtmlTag', array(
													'tag'  => 'ul',
													'id'   => 'recipeTags'
												),
											)
										)
									)
								);
		$tags->dummy3->clearValidators();
		
		//Serves
		$serves = new Zend_Form_Element_Text('serves');
		$serves->setLabel('Serves:')
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->addValidator('Digits')
				->setOrder(402)
				->setAttrib('size', '100')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		//Prep time
		$prep = new Zend_Form_Element_Text('prep_time');
		$prep->setLabel('Prep Time:')
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->addValidator(new Zend_Validate_Date('HH:mm'))
				->setOrder(403)
				->setAttrib('size', '100')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		//Cook time
		$cook = new Zend_Form_Element_Text('cook_time');
		$cook->setLabel('Cook Time:')
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->addValidator(new Zend_Validate_Date('HH:mm'))
				->setOrder(404)
				->setAttrib('size', '100')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		//Publish
		$publish = new Zend_Form_Element_Checkbox('publish');
		$publish->setLabel('Publish:')
				->removeDecorator('HtmlTag')
				->setCheckedValue(1)
				->setUncheckedValue(0)
				->setOrder(405)
				->setAttribs(array('style' => 'margin-bottom:20px;'));
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Submit')
				->removeDecorator('HtmlTag')
				->setOrder(406)
				->setAttrib('id', 'submitbutton')
				->setAttribs(array('style' => 'margin-left:-40px'));
		
		$this->addElements(array($title, $gallery_id, $gallery, $file, $ingredient_id, $ingredient_amount, $ingredient_unit, $ingredient, $ingredient_add, $ingredient_remove, $step_id, $step, $step_add, $step_remove, $tag_label, $tags, $serves, $prep, $cook, $publish, $submit));
	}
	
}

