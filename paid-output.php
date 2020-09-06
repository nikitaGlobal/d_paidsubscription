<div class="planks-wrapper">
    <div class="planks-list">
        <?php 
            global $wpdb;
            $paids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}paid_info WHERE type='sucrib'");
            foreach ($paids as $paid) { ?>
                <?php 
                    global $wpdb;
                    $date = $wpdb->get_var($wpdb->prepare("SELECT user_registered FROM $wpdb->users WHERE ID='$paid->user_id'"));
                    $date = explode(' ', $date);
                    $date = explode('-', $date[0]);
                    $userregdate = $date[0].'-'.$date[1].'-'.$date[2];
                ?>
                <div class="plank" style="display:-webkit-box;display:-ms-flexbox;display:flex;padding:2.5rem 6.5rem 2.5rem 2.875rem;margin:1.25rem 0;background:#f2f4f6;position:relative;line-height:150%">
                    <div class="plank__person" style="width:25%;padding: 0 2.5rem 0 0;">
                        <div class="person-wrap">
                            <div class="person__img" style="width:5.125rem;height5.125rem">
                                <img src="<?php echo get_avatar_url($paid->user_id, '48.7');?>" alt="Avatar" style="width: 100%">
                            </div>
                            <div class="person-tab" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin:1.25rem 0 0;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between">
                                <p style="font-size:1.0625rem;font-weight:600;color:#3d4461;cursor:pointer;-webkit-transition:color .3s ease-in-out;-o-transition:color .3s ease-in-out;transition:color .3s ease-in-out"><?php echo get_user_meta($paid->user_id, 'first_name', true);?></p>
                                <div class="pesron-tab__arrow-img person-arrow" style="position:relative;cursor:pointer;width:1rem;height:.5162rem">
                                    <img style="position:absolute;-webkit-transform:rotate(180deg);-ms-transform:rotate(180deg);transform:rotate(180deg);-webkit-transition:.3s ease-in-out;-o-transition:.3s ease-in-out;transition:.3s ease-in-out;display:block;width:100%;top:50%" src="<?php echo get_template_directory_uri();?>/assets/img/dest/table-arrow.svg" alt="Arrow">
                                </div>
                            </div>

                            <div class="person-tab-open" style="-webkit-transition:all 1s ease-in-out;-o-transition:all 1s ease-in-out;transition:all 1s ease-in-out;margin:.625rem 0 0;min-width:100%;max-height:0;opacity:0;overflow:hidden;margin-top:2.5rem">
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Откуда: <?php echo get_user_meta($paid->user_id, 'mestopolozhenie_22', true);?></p>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Зарегистрирован: <?php echo $userregdate;?></p>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Пол: <?php echo get_user_meta($paid->user_id, 'pol_16', true);?></p>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Возраст: 
                                <?php 
                                    $date_before = date_create(get_user_meta($paid->user_id , 'rcl_birthday', true));
                                    $date_after = date_create('now',new DateTimeZone('Europe/Moscow'));
                                    $interval = date_diff($date_before, $date_after);
                                    echo $interval->format('%y') . ' [' . get_user_meta($paid->user_id, 'rcl_birthday', true) . ']';
                                ?></p>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Провел на форуме: 
                                    <?php
                                        $daate_before = date_create($userregdate);
                                        $daate_after = date_create('now',new DateTimeZone('Europe/Moscow'));
                                        $intervaal = date_diff($daate_before, $daate_after);
                                        $y = $intervaal->format('%y');
                                        $M = $intervaal->format('%M');
                                        $D = $intervaal->format('%D');
                                        if($y > 0):
                                            echo ($y . ' год ');
                                        elseif($y > 4):
                                            echo ($y . ' лет ');
                                        endif;

                                        if($M === 01):
                                            echo ($M . ' месяц ');
                                        elseif($M > 00 && $M < 05):
                                            echo ($M . ' месяца ');
                                        elseif($M > 04):
                                            echo ($y . ' месяцев ');
                                        endif;

                                        if($D === 00 && $M === 00 && $y === 0):
                                            echo ('только зарегистрировался');
                                        elseif($D === 01 && $M === 00 && $y === 0):
                                            echo ($D . ' день');
                                        elseif($D > 00 && $D < 05):
                                            echo ($D . ' дня');
                                        elseif($D > 04):
                                            echo ($D . ' дней');
                                        endif;
                                    ?> 
                                </p>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Последний визит:</p>
                            </div>

                        </div>
                    </div>

                    <div class="plank__message" style="width:75%;border-left:.0625em solid #ddd;padding:0 0 0 2.5rem">
                        <div class="plank__message-clock" style="margin:0 0 1.25rem;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center">
                            <span style="width:1rem;height:1rem"><img style="width:100%" src="<?php echo get_template_directory_uri();?>/assets/img/dest/clock.svg" alt="Clock"></span>
                            <span style="margin: 0 0 .125em .4375rem;">
                            <?php
                                $daate_before = date_create($paid->start_date);
                                $daate_after = date_create('now',new DateTimeZone('Europe/Moscow'));
                                $intervaal = date_diff($daate_before, $daate_after);
                                $y = $intervaal->format('%y');
                                $M = $intervaal->format('%M');
                                $D = $intervaal->format('%D');
                                if($y > 0):
                                    echo ($y . ' год назад');
                                elseif($y > 4):
                                    echo ($y . ' лет назад');
                                endif;

                                if($M == 01):
                                    echo ($M . ' месяц назад');
                                elseif($M > 00 && $M < 05):
                                    echo ($M . ' месяца назад');
                                elseif($M > 04):
                                    echo ($y . ' месяцев назад');
                                endif;

                                if($D == 00 && $M == 00 && $y == 0):
                                    echo ('подписку оформил сегодня');
                                elseif($D == 01 && $M == 00 && $y == 0):
                                    echo ($D . ' день назад');
                                elseif($D > 00 && $D < 05):
                                    echo ($D . ' дня назад');
                                elseif($D > 04):
                                    echo ($D . ' дней назад');
                                endif;
                            ?>
                            </span>
                        </div>
                        <div class="plank__message-description">
                            <?php if(get_user_meta($otvet_user_id, 'pol_16', true) === 'Мужской'):?>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Подписку приобрёл: <strong style="font-weight:400;color: #ff5952;"><?php echo $paid->start_date?>
                            <?php elseif(get_user_meta($otvet_user_id, 'pol_16', true) === 'Женский'):?>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Подписку приобрела: <strong style="font-weight:400;color: #ff5952;"><?php echo $paid->start_date?>
                            <?php else:?>
                                <p style="font-weight:400;font-size:.875rem;color:#3d4461">Подписку приобрел(а): <strong style="font-weight:400;color: #ff5952;"><?php echo $paid->start_date?>
                            <?php endif;?></strong></p>
                            <p style="font-weight:400;font-size:.875rem;color:#3d4461">Подписка действует: до <strong style="font-weight:400;color: #ff5952;"><?php echo $paid->end_date?></strong></p>
                        </div>
                    </div>
                </div>
            <?php } 
        ?>
    </div>
</div>
