<?php use_helper('Payment'); ?>

<h1>Sample : Pay with the Paybox Direct product</h1>
<p>
  The button "Pay with Paybox" below will let you pay using the following information.
</p>

<?php include_partial('parameters', array('transaction' => $transaction)); ?>

<br />

<?php echo payment_form_tag_for($transaction->getGateway()); ?>
  <input type="submit" value="Pay with Paybox">
</form>