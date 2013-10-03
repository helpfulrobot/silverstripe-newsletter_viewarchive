<?php

/**
 * This pays allows you to display old newsletters.
 *
 **/

class NewsletterArchivePage extends Page {

	static $icon = "newsletter_viewarchive/images/treeicons/NewsletterArchivePage";

	public static $db = array(
		"ShowHistoricList" => "Boolean"
	);

	public static $has_one = array(
		"NewsletterType" => "NewsletterType"
	);

	public static $defaults = array(
		"ShowHistoricList" => true
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		if($this->ShowHistoricList) {
			if($types = DataObject::get("NewsletterType")) {
				$array = $types->toDropDownMap($index = 'ID', $titleField = 'Title', $emptyString = " -- Please select newsletter --");
				if($array && count($array)) {
					$fields->addFieldToTab("Root.Content.Newsletter", new CheckboxField("ShowHistoricList", "Show Historic List of Sent Newsletters?"));
					$fields->addFieldToTab("Root.Content.Newsletter", new DropdownField("NewsletterTypeID", "Select Newsletter", $array));
				}
			}
		}
		return $fields;
	}
}

class NewsletterArchivePage_Controller extends Page_Controller {

	protected $newsletterUniqueCode = "";

	function NewsletterList() {
		if($this->ShowHistoricList) {
			if($this->NewsletterTypeID) {
				return DataObject::get("Newsletter", "\"Status\" = 'Send' AND \"ParentID\" = ".$this->NewsletterTypeID);
			}
		}
	}

	function Newsletter() {
		if($this->newsletterUniqueCode) {
			return DataObject::get_one("Newsletter", "UniqueCode = '".$this->newsletterUniqueCode."'");
		}
	}

	function showonenewsletter($request) {
		if($newsletterUniqueCode = Convert::raw2sql($request->Param("ID"))) {
			$this->newsletterUniqueCode = $newsletterUniqueCode;
			if($newsletter = $this->Newsletter()) {
				$this->Title = $newsletter->Subject;
				$this->MetaTitle = $newsletter->Subject;
				$this->Content = $newsletter->Content;
				return array();
			}
		}
		return $this->httpError(404);
	}

}
