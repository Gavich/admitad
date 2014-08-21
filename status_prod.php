<?php
include './app/Mage.php';
Mage::app();
Mage::getSingleton('core/session', array('name'=>'adminhtml'));
$session = Mage::getSingleton('admin/session');;
if ($session->getUser()){

    if(isset($_POST['change'])){
        $storeid = Mage::app()->getStore()->getId();
        $status = (int)$_POST['status'];
        $search = ($status == 1)? 2 : 1;
            $products = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($storeid)
                ->addAttributeToSelect('id')
                ->addAttributeToFilter('status', array('eq' => (int)$search))
                ->setPageSize(3000)
                ->setCurPage(1);

        $i = (isset($_POST['store']))? $_POST['store'] : 0;

        foreach($products as $product)
        {
            $i++;
            Mage::getModel('catalog/product_status')->updateProductStatus($product -> getId(), $storeid, $status);
        }
    }
    ?>
    <form action="status_prod.php" method="post">
       <fieldset style="width: 200px">
           <legend>Change status product</legend>
           <?php if(isset($i)){ ?>
           <h4>Status change for <?php echo $i ?> products</h4>
           <input type="hidden" name="store" value="<?php echo $i ?>">
           <?php } ?>
           <div>
               <select name="status" id="status">
                   <option value="1">Enable</option>
                   <option value="2">Disable</option>
               </select>
               <label for="status">Select status</label>
           </div>
           <br>
           <div>
               <button type="submit" value="Change" name="change">Change</button>
           </div>
        </fieldset>
    </form>
<?php
}else{
    header('Location: ./');
}


