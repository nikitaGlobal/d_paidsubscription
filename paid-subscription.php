<?php
/*
    Plugin Name: Платная подписка
    Description: Ограничение контента
    Version: 1.0
    Author: D
*/

function create_paid_info()
{
    $checkkey = 'dyu1cpi';
    if (get_option($checkkey)) {
        return;
    }
    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->get_blog_prefix() . 'paid_info';
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

    $sql = "CREATE TABLE {$table_name} IF NOT EXISTS (
        id int(11) NOT NULL AUTO_INCREMENT,
        type varchar(15) NOT NULL default 'sucrib',
        user_id int(15) NOT NULL,
        price int(15) NOT NULL,   
        start_date date NOT NULL,
        end_date date NOT NULL,
	KEY payment_id (payment_id),
        KEY user_id (user_id),
	PRIMARY KEY (ID),
    )
    {$charset_collate};";

    dbDelta($sql);
    update_option($checkkey, true, false);
}
create_paid_info();

function createDownloadsBD()
{
    global $wpdb;
    $table_name = '' . $wpdb->prefix . 'user_downloads';
    $sql = "CREATE TABLE $table_name IF NOT EXISTS (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id varchar(255) DEFAULT NULL,
        UNIQUE KEY id (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
createDownloadsBD();

function update_table()
{
    $checkkey='dyu1ut';
    if (get_option($checkkey)) {
        return;
    }
    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->prefix . 'rmag_pay_results';

    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $collate .= " COLLATE $wpdb->collate";
        }
    }

    $sql = "CREATE TABLE {$table_name} IF NOT EXISTS (
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            payment_id INT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            pay_amount VARCHAR(20) NOT NULL,
            time_action DATETIME NOT NULL,
            pay_system VARCHAR(100) NOT NULL,
            pay_type VARCHAR(100) NOT NULL,
            row_type VARCHAR(100) NOT NULL,
            PRIMARY KEY  id (id),
            KEY payment_id (payment_id),
            KEY user_id (user_id)
    )$collate;";
    update_option($checkkey, true, false);
    dbDelta($sql);
}
update_table();

add_action('after_setup_theme', 'paidFunction');
function paidFunction()
{
    global $wpdb;

    $nots = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rmag_pay_results WHERE row_type=''");

    foreach ($nots as $not) {
        if ($not->pay_type === 'sucrib') {
            $sucrib = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'sucrib_price' AND meta_value = " . round($not->pay_amount) . " ");
            $sucrib_period = get_post_meta($sucrib, 'sucrib_period')['0'];
            $sucrib_day = get_post_meta($sucrib, 'sucrib_day')['0'];

            if ($sucrib_day === 'год') {
                $sucrib_per = "year";
            } elseif ($sucrib_day === 'месяц') {
                $sucrib_per = "month";
            } elseif ($sucrib_day === 'неделя') {
                $sucrib_per = "week";
            } elseif ($sucrib_day === 'день') {
                $sucrib_per = "days";
            };

            $user_paid = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}paid_info WHERE user_id = $not->user_id");

            if ($user_paid !== NULL) {
                $wpdb->update(
                    $wpdb->prefix . 'paid_info',
                    array('end_date' => date('Y-m-d', strtotime("$user_paid->end_date +" . intval($sucrib_period) . " $sucrib_per"))),
                    array('user_id' => $not->user_id),
                    array('%s')
                );
            } else {
                $wpdb->insert(
                    $wpdb->prefix . 'paid_info',
                    array('user_id' => $not->user_id, 'price' => round($not->pay_amount), 'start_date' => date("Y-m-d"), 'end_date' => date("Y-m-d", strtotime("+$sucrib_period $sucrib_per")), 'type' => 'sucrib'),
                    array('%d', '%d', '%s', '%s', '%s')
                );
            };

            $wpdb->update(
                $wpdb->prefix . 'rmag_pay_results',
                array('row_type' => 'added'),
                array('payment_id' => $not->payment_id),
                array('%s')
            );
        } elseif ($not->pay_type === 'baze') {
            $wpdb->insert(
                $wpdb->prefix . 'paid_info',
                array('user_id' => $not->user_id, 'price' => $not->pay_summ, 'start_date' => date("Y-m-d"), 'type' => 'pay'),
                array('%d', '%d', '%s',  '%s')
            );

            $to = get_user_by('ID', $not->user_id)->user_email;
            $subject = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'subject_mail'");
            $message = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'message_mail'") . '<br>' . $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'link_mail'");
            $headers = array(
                'От: <' . $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'from_mail'") . '>',
                'content-type: text/html'
            );

            wp_mail($to, $subject, $message, $headers);

            $wpdb->update(
                $wpdb->prefix . 'rmag_pay_results',
                array('row_type' => 'added'),
                array('payment_id' => $not->payment_id),
                array('%s')
            );
        } else {
            $wpdb->update(
                $wpdb->prefix . 'rmag_pay_results',
                array('row_type' => 'unknow'),
                array('payment_id' => $not->payment_id),
                array('%s')
            );
        }
    }
}

add_action('after_setup_theme', 'paiddelFunction');
function paiddelFunction()
{
    global $wpdb;
    $datte = date('Y-m-d');
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}paid_info WHERE end_date <= '$datte' AND type='sucrib'");
    if (count($results) !== 0) {
        foreach ($results as $result) {
            $wpdb->delete($wpdb->prefix . 'paid_info', array('id' => $result->id));
        }
    }
}

add_action('admin_menu', 'register_custom_paid_act');
function register_custom_paid_act()
{
    add_menu_page('Подписки', 'Оплаченные подписки', 'manage_options', 'paid-subscription/paid-output.php', '', 'dashicons-tickets', 3);
}
