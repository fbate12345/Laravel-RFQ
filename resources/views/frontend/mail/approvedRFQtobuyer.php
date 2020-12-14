<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body>
    <div style="padding-left: 10%; padding-right: 10%;">
        <a class="ps-logo" href="https://rfq.mambodubai.com">
            <img src="https://rfq.mambodubai.com/images/logo2_1.png" alt="" style="width: 130px;">
        </a>
        <hr/>
        <h1 style="color: #476B91;">Welcome <?= $name ?></h1><br>
        <h4>Your request for the <a href="<?= $product_link ?>">product</a> has been approved. <br> The details are as follows - </h4> <br>

        <h4>Company Name - <?= $company_name ?></h4>
        <h4>Product Name - <?= $rfq->product_name ?></h4>
        <h4>Volume - <?= $rfq->volume ?></h4>
        <h4>Destination - <?= $rfq->port_of_destination ?></h4>
        <h4>Additional Information - <?= $rfq->additional_information ?></h4>
		<?php 
            if (@$file_link) { ?>
                <h4>File - <a href="<?= $file_link ?>">Check the attachment</a></h4>
        <?php } ?>
        
        <h4>Date of request - <?= $rfq->sign_date ?></h4><br>
    </div>
</body>

</html>