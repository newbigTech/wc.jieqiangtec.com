<?php defined('IN_IA') or exit('Access Denied');?>
<div class="form-group" >
    <label class="col-sm-2 control-label">核销次数</label>
    <div class="col-sm-6 col-xs-12">
	   <?php if( ce('goods' ,$item) ) { ?>
			<div class="input-group">
				<input type="text" name="verifygoodsnum" id="verifygoodsnum" class="form-control" value="<?php  echo $item['verifygoodsnum'];?>" />
				<span class="input-group-addon">次</span>
			</div>
			<span class="help-block">此商品可以核销次数,不填或填写0及以下为默认不限次数</span>
		<?php  } else { ?>
        	<div class='form-control-static'><?php  echo $item['verifygoodsnum'];?> 次</div>
        <?php  } ?>
    </div>
</div>

<div class="form-group minbuy">
    <label class="col-sm-2 control-label">有效期天数</label>
    <div class="col-sm-6 col-xs-12">
	   <?php if( ce('goods' ,$item) ) { ?>
			<div class="input-group">
				<input type="text" name="verifygoodsdays" id="verifygoodsdays" class="form-control" value="<?php  echo $item['verifygoodsdays'];?>" />
				<span class="input-group-addon">天</span>
			</div>
			<span class="help-block">自购买之日起多少天内有效,不写默认365天</span>
		<?php  } else { ?>
			<div class='form-control-static'><?php  echo $item['verifygoodsdays'];?> 天</div>
        <?php  } ?>
    </div>
</div>