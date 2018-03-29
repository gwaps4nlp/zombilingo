<?php namespace App\Services\Html;

class FormBuilder extends \Collective\Html\FormBuilder {

	public function submit($value = null, $options = [])
	{
		return sprintf('
			<div class="form-group %s">
				%s
			</div>',
			empty($options) ? '' : $options[0],
			parent::submit($value, ['class' => 'btn btn-success'])
		);
	}

	public function destroy($text, $message, $class = null)
	{
		//return parent::submit($text, ['class' => 'btn btn-danger btn-block ' . ($class? $class:''), 'onclick' => 'return confirm(\'' . $message . '\')']);
		return parent::submit($text, ['class' => 'btn ' . ($class? $class:''), 'onclick' => 'return confirm(\'' . $message . '\')']);
	}

	public function control($type, $colonnes, $nom, $errors, $label = null, $valeur = null, $pop = null, $placeholder = '', $selected = '')
	{
		$attributes = ['id' => $nom,'class' => 'form-control', 'placeholder' => $placeholder, 'selected' => $selected];
		return sprintf('
			<div class="form-group %s %s">
				%s
				%s
				%s
				%s
			</div>',
			($colonnes == 0)? '': 'col-lg-' . $colonnes,
			$errors->has($nom) ? 'has-error' : '',
			$label ? $this->label($nom, $label, ['class' => 'control-label']) : '',
			$pop? '<a href="#" tabindex="0" class="badge badge-warning pull-right" data-toggle="popover" data-trigger="focus" title="' . $pop[0] .'" data-content="' . $pop[1] . '"><span>?</span></a>' : '',
			call_user_func_array(['Form', $type], ($type == 'password')? [$nom, $attributes] : [$nom, $valeur, $attributes]),
			$errors->first($nom, '<small class="help-block">:message</small>')
		);
	}

	public function check($name, $label)
	{
		return sprintf('
			<div class="checkbox col-lg-12">
				<label>
					%s%s
				</label>
			</div>',
			parent::checkbox($name),
			$label
		);		
	}
	public function radioInLine($name, $value, $label, $default=null,$errors=null)
	{
		$checked = ($value==$default)?true:false;
		return sprintf('
			<div class="form-group %s">
			<div class="checkbox-inline">
				<label>
					%s %s
				</label>
				%s
			</div>
			</div>',
			$errors->has($name) ? 'has-error' : '',
			parent::radio($name,$value, $checked),
			$label,
			$errors->first($name, '<small class="help-block">:message</small>')
		);		
	}

	public function checkHorizontal($name, $label, $value)
	{
		return sprintf('
			<div class="form-group">
				<div class="checkbox">
					<label>
						%s%s
					</label>
				</div>
			</div>',
			parent::checkbox($name, $value),
			$label
		);		
	}

	public function selection($nom, $list = [], $attributes = null)
	{
		$selected = isset($attributes['selected'])?$attributes['selected']:null;
		return parent::select($nom, $list, $selected, $attributes);
	}

}
