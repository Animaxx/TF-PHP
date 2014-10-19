<?php
/**
 * ITFView 
 * @author Animax
 */
interface ITFView {
	public function addDisplayVar($name, $var);
	public function addDisplayArray($DisplayList);
	public function removeDisplayVar($name);
	public function desplayView();
}