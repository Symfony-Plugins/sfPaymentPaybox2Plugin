<?php
  if(isset($transaction)) {
  	$parameters = $transaction->getFields();
  } elseif(count($parameters = $sf_request->getPostParameters()) > 0) {
  	$parameters = $sf_request->getPostParameters();
  }
  	else {
  	 $parameters = $sf_request->getGetParameters();
  }
?>
<?php if(count($parameters)):?>
<table border="1">
<?php foreach($parameters as $key => $field): ?>
  <?php // bold for specific code (easier to read)  ?>
  <?php if (in_array($key, array('PBX_DEVISE', 'PBX_TOTAL', 'PBX_CMD'))):?>
  <tr style="font-weight: bold; font-size: 1.1em;">
  <?php else:?>
  <tr>
  <?php endif;?>
    <td><?php echo $key; ?></td>
    <td><?php echo $field; ?></td>
  </tr>
<?php endforeach;?>
</table>
<?php else:?>
<em>No parameter</em>
<?php endif;?>