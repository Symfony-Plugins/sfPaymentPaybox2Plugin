<?php  
class sfPaymentPayboxTestTools {
	static public function getFormUri($dom) {
    $xpath = new DomXpath($dom);
    if ($forms = $xpath->query('//form'))
    {     
      foreach($forms as $form)
      {
        break;
      }
    }

    return $form->getAttribute('action');
	}
	
	static public function getFormParams($dom) {
		$xpath = new DomXpath($dom);
    if ($forms = $xpath->query('//form'))
    {     
      foreach($forms as $form)
      {
        break;
      }
    }

    $url = $form->getAttribute('action');
    $method = $form->getAttribute('method') ? strtolower($form->getAttribute('method')) : 'get';
    // merge form default values and arguments
    $defaults = array();
    foreach ($xpath->query('descendant::input | descendant::textarea | descendant::select', $form) as $element)
    {
      $elementName = $element->getAttribute('name');
      $nodeName    = $element->nodeName;
      $value       = null;
      if ($nodeName == 'input' && ($element->getAttribute('type') == 'checkbox' || $element->getAttribute('type') == 'radio'))
      {
        if ($element->getAttribute('checked'))
        {
          $value = $element->getAttribute('value');
        }
      }
      else if (
        $nodeName == 'input'
        &&
        (($element->getAttribute('type') != 'submit' && $element->getAttribute('type') != 'button') || $element->getAttribute('value') == $name)
        &&
        ($element->getAttribute('type') != 'image' || $element->getAttribute('alt') == $name)
      )
      {
        $value = $element->getAttribute('value');
      }
      else if ($nodeName == 'textarea')
      {
        $value = '';
        foreach ($element->childNodes as $el)
        {
          $value .= $dom->saveXML($el);
        }
      }
      else if ($nodeName == 'select')
      {
        if ($multiple = $element->hasAttribute('multiple'))
        {
          $elementName = str_replace('[]', '', $elementName);
          $value = array();
        }
        else
        {
          $value = null;
        }

        $found = false;
        foreach ($xpath->query('descendant::option', $element) as $option)
        {
          if ($option->getAttribute('selected'))
          {
            $found = true;
            if ($multiple)
            {
              $value[] = $option->getAttribute('value');
            }
            else
            {
              $value = $option->getAttribute('value');
            }
          }
        }

        // if no option is selected and if it is a simple select box, take the first option as the value
        if (!$found && !$multiple)
        {
          $value = $xpath->query('descendant::option', $element)->item(0)->getAttribute('value');
        }
      }

      if (null !== $value)
      {
        $defaults = self::parseArgumentAsArray($elementName, $value, $defaults);
      }
    }

    // create request parameters
    $arguments = sfToolkit::arrayDeepMerge($defaults, array(), array());
    return $arguments;
	}
    
  static public function parseArgumentAsArray($name, $value, $vars)
  {
    if (false !== $pos = strpos($name, '['))
    {
      $var = &$vars;
      $tmps = array_filter(preg_split('/(\[ | \[\] | \])/x', $name));
      foreach ($tmps as $tmp)
      {
        $var = &$var[$tmp];
      }
      if ($var)
      {
        if (!is_array($var))
        {
          $var = array($var);
        }
        $var[] = $value;
      }
      else
      {
        $var = $value;
      }
    }
    else
    {
      $vars[$name] = $value;
    }
    
    return $vars;
  }
  
  static public function getLastAnchor($dom) {
  	$xpath = new DomXpath($dom);
    if ($as = $xpath->query('//a'))
    {     
      foreach($as as $a)
      {
        continue;
      }
    }

    return $a->getAttribute('href');
  }
  
  static public function getAnchor($dom, $href) {
    $xpath = new DomXpath($dom);
    if ($as = $xpath->query('//a'))
    {     
      foreach($as as $a)
      {
        if(substr_count($a->getAttribute('href'),$href))
          return $a->getAttribute('href');
      }
    }
    return NULL;
  }
}