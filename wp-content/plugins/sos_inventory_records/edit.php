<?php


define('SOS_PLUGIN_DIR', plugin_dir_path(__FILE__));

$producstData = get_vendor_data($_GET["record_id"]);
$result = json_decode($producstData, true);

?>
<form id="formEditData" action="<?php echo add_query_arg('update_sos_vendor', true); ?>" method="POST">
    <input type="hidden" name="id" value="<?php echo $result["data"]["id"]; ?>">
    <div class="row">
        <div class="mb-3 col-xxl-6">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $result["data"]["name"]; ?>" class="form-control" />
        </div>
        <div class="mb-3 col-xxl-6">
            <label>Company Name</label>
            <input type="text" name="companyName" value="<?php echo $result["data"]["companyName"]; ?>" class="form-control" />
        </div>
        <div class="mb-3 col-xxl-6">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo $result["data"]["phone"]; ?>" class="form-control" />
        </div>
        <div class="mb-5 col-xxl-6">
            <label>Email</label>
            <input type="text" name="email" value="<?php echo $result["data"]["email"]; ?>" class="form-control" />
        </div>
        <div class="mb-3 col-xxl-12 text-right">
            <input type="submit" name="update" class="btn btn-primary" value="Update" id="updatedata" />
            <a href="<?php echo site_url() ?>" class="btn btn-default">Cancel</a>
        </div>
    </div>
</form>