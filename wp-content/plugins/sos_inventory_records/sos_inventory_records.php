<?php

/*
 
Plugin Name: Fetch SOS Inventory Data
Description: Plugin is used to fetch data from SOS Inventory API.
Version: 1.0.0
Author: Muzammil
Author URI: https://www.sosinventory.com/
Lisence: GPL V2
 
*/

if (!defined('ABSPATH')) :
    die("you can not access pluign files directly");
endif;

define('SOS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SOS_PLUGIN_URL', plugin_dir_url(__FILE__));

function sos_wp_enqueue_scripts()
{
    wp_enqueue_script("sos_dqtq_scripts",  SOS_PLUGIN_URL . "js/sos_custom.js", array("jquery"), "1.0.0", false);
}
add_action("wp_enqueue_scripts", "sos_wp_enqueue_scripts");

function make_curl_call($url, $method, $headers)
{
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $response;
}

define(
    "HEADERS",
    [
        "Content-Type:application/x-www-form-urlencoded",
        "Host:api.sosinventory.com",
        "Authorization:Bearer jN2a5hKiTNn_U7UMLpGr-YCDttmudk80wssItpQm1z8mnXrXuUpXaq2Ok2Wfi700U2n_vZkobNrAkMATrHk-1neZjAfwpNHiFXUPlxB6okPYSuonbSbD1cF3qrBuONBUJpQeS0h4oZyu5v5CkBL7meYSZeAWuhK62J39pyTiivS_fVVtgpSPB4IQXjX58OBs1K_gr9CFuBuQGPStr0DR97fPOYVl53M6-u2CC03FXgR496TF3a1m42InkAxsM572L9i9JZHTbMbvKDqspJUi3rs1Z1iE-Goqhe_GuxYhUUERvRdE"
    ]
);

function fetchProducts()
{
    ob_start();

    if (!isset($_GET["record_id"])) {
        $url = "https://api.sosinventory.com/api/v2/vendor";
        $method = "GET";
        $producstData = make_curl_call($url, $method, HEADERS);
        $results = json_decode($producstData, true);
?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th colspan="2" class="text-center">Actions</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    // echo "<pre>";
                    // print_r($results["data"]);
                    // exit;
                    if (!empty($results["data"])) {
                        foreach ($results["data"] as $key => $result) { ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo $result["name"]; ?></td>
                                <td><?php echo $result["companyName"]; ?></td>
                                <td><?php echo $result["phone"]; ?></td>
                                <td><?php echo $result["email"]; ?></td>
                                <td>
                                    <a href="<?php echo '?record_id=' . $result['id']; ?>" class="btn btn-primary w-100">Edit</a>
                                </td>
                                <td>
                                    <a data-id="<?php echo $result['id']; ?>" href="javascript:;" class="btn btn-danger w-100 deleteData">Delete</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6">
                                <h6 class="text-center">No record found</h6>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
        </div>
<?php } else {
        require_once(SOS_PLUGIN_DIR . "edit.php");
    }

    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}
add_shortcode('show_sos_inventory', 'fetchProducts');

function get_vendor_data($vendor_id)
{

    $url = "https://api.sosinventory.com/api/v2/vendor/" . $vendor_id;
    $method = "GET";

    $producstData = make_curl_call($url, $method, HEADERS);
    return $producstData;
}

function update_vendor_data($vendor_id, $data)
{

    $url = "https://api.sosinventory.com/api/v2/vendor/" . $vendor_id;
    $method = "PUT";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, HEADERS);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($curl);

    return array(
        'code' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
        'data' => $response,
    );
}

function update_sos_vendor()
{
    if (!isset($_GET['update_sos_vendor'])) return;

    $vendorData = get_vendor_data($_GET["record_id"]);
    $result = json_decode($vendorData, true);
    $result = $result['data'];
    $result["name"] = $_POST['name'];
    $result["companyName"] = $_POST['companyName'];
    $result["phone"] = $_POST['phone'];
    $result["email"] = $_POST['email'];

    $data = update_vendor_data($_GET["record_id"], $result);
    wp_redirect(remove_query_arg(['record_id', 'update_sos_vendor']));
    exit();
}
add_action('template_redirect', 'update_sos_vendor');


function sos_custom_cron_schedule($schedules)
{
    $schedules['every_hours'] = array(
        'interval' => 3600,
        'display'  => __('Every hours'),
    );
    return $schedules;
}
add_filter('cron_schedules', 'sos_custom_cron_schedule');

if (!wp_next_scheduled('sos_cron_hook')) {
    wp_schedule_event(time(), 'every_hours', 'sos_cron_hook');
}

add_action('sos_cron_hook', 'sos_cron_function');

function sos_cron_function()
{
    fetchProducts();
}
