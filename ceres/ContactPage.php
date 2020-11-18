<?php

#
# Ceres
# (c) 2020 Simon Strudwick
# 

// Config

class ContactPage extends Page {
	private $email;
	private $sendAutoReply;
	public $categories;
	private $errorMessage;
	
	function __construct($sendToEmail, $sendAutoreply) {
		$this->email = new Email(true);		
		$this->email->to = $sendToEmail;
		$this->email->sendAutoReply = $sendAutoreply;
		$pageName = 'contact_intro';
		
		if (!empty($_POST)) {
			$isValid = $this->email->isValid();
			if ($isValid === true) {
				if ($this->email->send()) {
					$pageName = 'contact_submitted';
				}
			} else {
				$this->errorMessage = $isValid;
			}	
		}
		
		parent::__construct($pageName, 'fragment');
		
		$this->categories = [];
	}
	
	function html() {
		$html = parent::html();
		if (!$this->email->isSent) {
			$html .= $this->getForm();
		}
		
		return $html;
	}
	
	function getForm() {
		$email = $this->email;
		$formHTML = '<form action="/page/contact" method="POST" accept-charset="utf-8">'.PHP_EOL;
		
		if ($this->errorMessage !== null) {
			$formHTML .= 'p class="contact-form-error">'.$this->errorMessage.'</p>'.PHP_EOL;
		}
		
		$formHTML .= '	<fieldset>'.PHP_EOL;
		$formHTML .= '		<p><label for="email_from_name">Name</label><input id="email_from_name" name="email_from_name" type="text" value="'.$email->fromName.'"></p>'.PHP_EOL;
		$formHTML .= '		<p><label for="email_from">Email address</label><input id="email_from" name="email_from" type="text" value="'.$email->from.'"></p>'.PHP_EOL;
		
		foreach ($this->categories as $category) {
			$formHTML .= '		<p><input type="radio" id="'.$category.'" name="category" value="'.$category.'" checked>';
			$formHTML .= ' <label for="'.$category.'">'.$category.'</label></p>'.PHP_EOL;
		}
		
		$formHTML .= '		<p><label for="email_subject">Subject</label> <input id="email_subject" name="email_subject" type="text" value="'.$email->subject.'"></p>'.PHP_EOL;
		$formHTML .= '		<p><textarea id="email_content" name="email_content" rows="10">'.$email->body.'</textarea></p>'.PHP_EOL;
		$formHTML .= '		<p><input type="submit" id="submit-button" name"submit-button" value="Send"></p>'.PHP_EOL;
		$formHTML .= '	</fieldset>'.PHP_EOL;
		$formHTML .= '</form>'.PHP_EOL;
		
		return $formHTML;
	}
	
	function processForm() {
		$emailIsValid = $this->email.isValid();
		
		if ($emailIsValid !== true) {
			$this->errorMessage = $emailIsValid;
			return false;
		}
		
		return $this->email->send();
	}
}

class Email {
	
	public $from;
	public $fromName;
	public $to;
	public $subject;
	public $body;
	public $isSent;
	
	function __construct($prefillFromPost = false) {
		if ($prefillFromPost) {
			$this->from = filter_input(INPUT_POST, 'email_from', FILTER_SANITIZE_EMAIL);
			$this->fromName = filter_input(INPUT_POST, 'email_from_name', FILTER_SANITIZE_STRING);
			$this->subject = filter_input(INPUT_POST, 'email_subject', FILTER_SANITIZE_STRING);
			$this->body = filter_input(INPUT_POST, 'email_content', FILTER_SANITIZE_STRING);
		}
		
		$this->isSent = false;
	}
	
	function isValid() {
		if (!validateMail($this->from)) {
			return "Email is invalid";
		}
		if (!validateMail($this->to)) {
			return "Something's gone wrong...";
		}
		if ($this->subject === '') {
			return "Please enter a subject";
		}
		if ($this->body === '') {
			return "Please enter a message.";
		}
		
		return true;
	}
	
	function send() {
		if (!$this->isValid()) {
			return false;
		}
		
		if ($this->fromName !== null && $this->fromName !== '') {
			$headers = "From: $this->fromName <$this->from>".PHP_EOL;
		} else {
			$headers = "From: $this->from".PHP_EOL;
		}
		
		$this->isSent = mail($this->to, $this->subject, $this->body, $headers);
		return $this->isSent;
	}
}

// Returns true if $emailAddress is formatted as an email address 1-z@1-z.1-z else returns false
function validateMail($email) {
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// invalid email address
		return false;
	}

	$domain = substr(strrchr($email, '@'), 1);
	if (!checkdnsrr($domain, 'MX')) {
		// domain is not valid
		return false;
	}
	return true;
}

?>