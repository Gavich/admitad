<?php
/**
 * Created by PhpStorm.
 * User: User Home
 * Date: 03.09.14
 * Time: 20:54
 */
header('Content-Type: text/html; charset=utf-8');
include './app/Mage.php';
$mage = Mage::app();
Mage::getSingleton('core/session', array('name'=>'adminhtml'));
$session = Mage::getSingleton('admin/session');

$model  = Mage::getModel('amshopby/value');
$collection = $model->getCollection()->addFilter('img_small', 'multi-color.png')->load();

$attributeOptionArray=array();
$opts_attr = Mage::getModel('eav/config')->getAttribute('catalog_product', 'color_filter');
foreach ( $opts_attr->getSource()->getAllOptions(true, true) as $option){
    $attributeOptionArray[$option['value']] = $option['label'];
}
$color = (Mage::app()->getRequest()->isPost())? Mage::app()->getRequest()->getPost('color') : null;
?>
<form action="color.php" method="post" >
    <fieldset style="width: 400px">
        <legend>Цвета которых нет</legend>
        <label>
            <select name="color" onchange="this.form.submit()" size="10">
                <?php
        foreach($collection as $item){
            $title = $item->getMetaTitle();
            $idOption = $item->getOptionId();
                ?>
                <option value="<?php echo $idOption?>" <?php if($color and $color == $idOption){ echo "selected"; }?>><?php echo $title?></option>
         <?php }?>
            </select>

        </label>
    </fieldset>
</form>
<?php
if($color){
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter("color_filter",array('finset'=>$color))
            ->setPageSize(5)
            ->addAttributeToSelect('*');
        ?>
    <table>
        <caption><h3>Цвет:  <?php echo $attributeOptionArray[$color] ?></h3></caption>
        <tr>
        <?php
        foreach($products as $_item){
            ?>
                <td align="center" style="width: 300px; margin: 10px">
                    <a href="<?php echo $_item->getProductUrl() ?>" target="_blank">
                    <div>
                        <img src="<?php echo Mage::helper('catalog/image')->init($_item, 'small_image')->resize(356,546) ?>">
                    </div>
                    <?php echo $_item->getName() ?></a>
                </td>
    <?php
        }
    ?>
        </tr>
    </table>
<?php
}
?>

