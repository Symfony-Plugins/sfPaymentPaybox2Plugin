<?php use_helper("Form"); ?>

<div class="payment-error">
  <?php echo $sf_user->getFlash("error"); ?>
</div>

<div id="paybox-payment-form">
	<?php echo form_tag("sfPaymentPaybox/pay",array("name" => "form_pay")); ?>
	  <table>
	    <?php
	    /**
	     * Displaying the hidden fields
	     * @link http://trac.symfony-project.org/ticket/5975
	     */
	    $output = '';
	    foreach ($form->getFormFieldSchema() as $name => $field)
	    {
	      if ($field->isHidden())
	      {
	      	$att = $field->getWidget()->getAttributes();
	        $output .= $field->render(array('value' => $att['value']));
	      }
	    }
	
	    echo $output; 
	     ?>
	    <tr>
	      <th><?php echo $form['PORTEUR']->renderLabel() ?></th>
	      <td><?php echo $form['PORTEUR']->renderError() ?><?php echo $form['PORTEUR']->render() ?></td>
	    </tr>
	    <tr>
        <th>Expiry date (MM/YYYY)</th>
        <td><?php echo $form['DATEVAL']->renderError() ?><?php echo $form['DATEVAL']->render() ?></td>
      </tr>
	    <tr>
	      <th>Cryptogram : the last 3 digits on the back of your card</th>
	      <td><?php echo $form['CVV']->renderError() ?><?php echo $form['CVV']->render() ?></td>
	    </tr>
	    
	  </table>
	  <a href="<?php echo url_for("sfPaymentPaybox/cancel?amount=".$amount."&reference=".$reference) ?>"><?php echo image_tag('../sfPaymentPaybox2Plugin/images/ibs2_GBR_ANU.gif', array("alt" => "Cancel","style" => "border:0px;")) ?></a>
	  <?php echo image_tag('../sfPaymentPaybox2Plugin/images/ibs2_GBR_VAL.gif',array("alt" => "Validate","style" => "cursor:pointer;","onclick" => "document.form_pay.submit();")) ?>
	
	</form>
</div>