<?php
/**
 * -------------------------------Enroll Page Price for memberships----------------------------------
 */
function get_memberships($event) {

    $url = get_home_url() . "/wp-json/mp/v1/" . $event;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $headers = array();
    $headers[] = 'Memberpress-Api-Key: '.$MEMBERSHIP_API_KEY;
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($result, true);
}

function get_prices($memberships, $type){
    $saves = ''; $month = ''; $year = '';
    foreach ($memberships as $item) {

        if ($item['title'] == ucfirst($type) . ' – monthly'){
            $month = $item['price'];
        }
        if ($item['title'] == ucfirst($type) . ' – yearly') {
            $year = $item['price'];
        }
    }
    $saves = abs($year - ($month * 12));
    get_markup($month, $year, $saves, $type);
}

function get_markup($monthly, $yearly, $save, $type){
    ?> <div class="subscribe-block">
                <div class="options">
                    <div class="month">
                        <p>Monthly</p>
                        <p>$ <?php echo $monthly ?> /  month</p>
                    </div>
                    <label class="switch">
                      <input type="checkbox" class="<?php echo $type ?>-switcher" onclick="<?php echo $type ?>_trigger(this);">
                      <span class="slider round"></span>
                    </label>
                    <div class="year">
                        <p>Yearly</p>
                        <p>$ <?php echo $yearly ?> /  (you save <span class="save-price">$<?php echo $save ?></span>)</p>
                    </div>
                </div>
            <a class="<?php echo $type ?>-btn"></a>
            <script>
                function <?php echo $type ?>_trigger(el){
                   if (el.checked) {
                       document.querySelectorAll(".<?php echo $type ?>-btn").forEach(btn => { btn.innerHTML = `Enroll now for $` + '<?php echo $yearly ?>'
                                                                                 btn.href = "/register/<?php echo $type ?>-yearly/"});
                    } else {
                       document.querySelectorAll(".<?php echo $type ?>-btn").forEach(btn => { btn.innerHTML = `Enroll now for $` + '<?php echo $monthly ?>'
                                                                                 btn.href = "/register/<?php echo $type ?>-monthly/"});
                   }
                }
                document.querySelectorAll(".<?php echo $type ?>-switcher").forEach(elem => { <?php echo $type ?>_trigger(elem)});
            </script>
        </div><?php
}

function silver_packages_shortcode() {
    get_prices(get_memberships('memberships'), 'silver');
}

function gold_packages_shortcode() {
    get_prices(get_memberships('memberships'), 'gold');
}

add_shortcode('silver_packages', 'silver_packages_shortcode');
add_shortcode('gold_packages', 'gold_packages_shortcode');
