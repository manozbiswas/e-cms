<?php
/**
 * Created by JetBrains PhpStorm.
 * User: MONIR
 * Date: 9/12/13
 * Time: 7:33 PM
 * To change this template use File | Settings | File Templates.
 */

class M_category extends CI_Model
{
    var $user_language;
    var $user_language_id;
    public function __construct()
    {
        $this->db->query ("set character_set_results='utf8'");
        $this->user_language = get_language();
        $this->user_language_id = get_language_id();
    }

    public function getAllProduct($cat_id,$offset = 0,$limit = 20)
    {
        $query = "select tbl1.product_id,product_price,product_name,image_id,image_ext,special_price
                    from
                    (
                        select products.product_id as product_id,product_price,product_name
                        from  products,products_description
                        where products.product_id = products_description.product_id and category_id = $cat_id
                              and language_id = {$this->user_language_id} and product_status = 1 and  date_available <= curdate()
                    )as tbl1

                    left join

                    (
                        select product_id,image_id,min(image_ext) as  image_ext
                        from products_images
                        where product_id in(select product_id from  products where category_id = $cat_id and product_status = 1
                              and  date_available <= curdate())
                        group by product_id
                    )as tbl2

                    on tbl1.product_id = tbl2.product_id

                    left join

                    (
                        select product_id,special_price
                        from specials
                        where expire_date > curdate() and status = 1
                              and product_id in (select product_id from  products where category_id = $cat_id
                              and product_status = 1 and  date_available <= curdate())
                    ) as tbl3

                    on tbl1.product_id = tbl3.product_id

                    limit $offset,$limit";

        $q = $this->db->query($query);
        if($q->num_rows()>0)
        {
            foreach($q->result() as $row)
            {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getCatById($id)
    {
        $query = "select * from categories_description where language_id = 4 and category_id = ?";
        $q     = $this->db->query($query,$id);
        if($q->num_rows()>0)
        {
            foreach($q->result() as $row)
            {
                $data[] = $row;
            }
            return $data;
        }
    }

   /* public function countProductPerCat($cat_id)
    {
        $query = "select count(*) as product_count from products where category_id = $cat_id and date_available <= curdate() and product_status = 1";
        $q     = $this->db->query($query);
        if($q->num_rows()>0)
        {
            $row = $q->row_array();
            return $row['product_count'];
        }
    }
    */
}