<?php

/**
 * This pays allows you to display old newsletters.
 *
 **/

class NewsletterArchiveDecorator extends DataObjectDecorator {

	function extraStatics(){
		return array(
			'db' => array(
				"UniqueCode" => "Varchar(32)"
			),
			'casting' => array(
				"ViewingPage" => "SiteTree",
				"Link" => "Varchar"
			)
		);
	}

	/**
	 * Update the CMS fields specifically for Member
	 * decorated by this NewsletterRole decorator.
	 *
	 * @param FieldSet $fields CMS fields to update
	 */
	function updateCMSFields($fields) {
		if($link = $this->owner->getLink()){
			$fields->addFieldToTab("Root.Newsletter", new LiteralField("LinkLink", "<h3><a href=\"$link\">view online link</a></h3>"));
		}
		else {
			$fields->addFieldToTab("Root.Newsletter", new LiteralField("LinkLink", "<p>There is no preview link, you need to setup a Newsletter Archive Page specifically for this newsletter.</p>"));
		}
	}

	function getViewingPage() {
		return $this->owner->ViewingPage();
	}

	function ViewingPage() {
		if($this->owner->ParentID) {
			$parent = DataObject::get_by_id("NewsletterType", $this->owner->ParentID);
			if($parent) {
				return DataObject::get_one("NewsletterArchivePage", "NewsletterTypeID = ".$parent->ID);
			}
		}
	}

	function Link()  { return $this->getLink();}
	function getLink()  {
		$viewingPage = $this->owner->ViewingPage();
		if($viewingPage) {
			return Director::absoluteURL($viewingPage->Link("showonenewsletter")."/".$this->owner->UniqueCode."/");
		}
	}

	function onBeforeWrite(){
		if(!$this->owner->UniqueCode) {
			$this->owner->UniqueCode = uniqid();
		}
	}

}

class NewsletterArchiveDecorator_Email extends Extension {


	function updateNewsletterEmail($newsletter){
		$this->owner->populateTemplate(new ArrayData(array(
			'Link' => $this->owner->newsletter->Link(),
			'UnsubscribeLink' => $this->owner->UnsubscribeLink(),
			'Newsletter' => $this->owner->newsletter
		)));
	}

}
