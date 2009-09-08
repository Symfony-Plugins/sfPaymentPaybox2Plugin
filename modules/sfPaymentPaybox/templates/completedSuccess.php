<h1>Payment Success</h1>
<div>Thank you!</div>
<div>We have received your order and have started processing it. We will let you know as soon as it is being confirmed by Paybox.</div>
<br />
<table>
<tr>
<th>Transaction number: </th>
<td><?php echo $transaction_number ?></td>
</tr>
<tr>
<th>Reference: </th>
<td><?php echo $reference ?></td>
</tr>
<tr>
<th>Amount: </th>
<td><?php echo number_format($amount/100,2) ?></td>
</tr>
</table>