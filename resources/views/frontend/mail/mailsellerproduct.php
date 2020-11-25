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
        <h4>Your product(<a href="<?= $product_link ?>"><?= $product->name ?></a>) has been submitted successfully and it is pending for approval. You will be notified by email again once approved. <br> You product details are as follows -  </h4> <br>

        <h4>Company Name - <?= $company_name ?></h4>
        <h4>Product Name - <?= $product->product_name ?></h4>
        <h4>Category - <?= $category ?></h4>
        <h4>Unit - <?= $unitname ?></h4>
        <h4>MOQ - <?= $product->MOQ ?></h4>
		<h4>Description - <?= $product->description ?></h4>
        <h4>Price - <?= $product->price_from ?> <?= $localization_setting->currency ?> ~ <?= $product->price_to ?> <?= $localization_setting->currency ?></h4>
        
        <h4>Date of upload - <?= $product->sign_date ?></h4><br>
    </div>
</body>

</html>