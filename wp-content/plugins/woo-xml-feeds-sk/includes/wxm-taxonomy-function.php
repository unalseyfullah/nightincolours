<?php 


/**
 * Order category for 
 * category setting, create hierarachy category tree
 * 
 * @since 2.0.0
 */   
function custom_taxonomy_walker($taxonomy, $parent = 0){
    $out = '';
    $terms = get_terms(
                      'product_cat', 
                      array(
                            'parent' => $parent, 
                            'hide_empty' => 0, 
                            'orderby' => 'ASC'
                      )
    );
    if(count($terms) > 0)
    {
        foreach ($terms as $term)
        {
            $out .= $term->term_id .','. custom_taxonomy_walker($taxonomy, $term->term_id); 
        }
        return $out;
    }
    return;
}

  /**
   * Taxonomy pagination
   * for admin category setting   
   *
   * @since 2.0.0
   */           
  function tax_pagination($cat_list, $step = 50){
     $page = $_GET['page'];
     $catTerms = explode(',',$cat_list);
     $all = count($catTerms);
     $pages = ceil($all / $step);
     if(!empty($_GET['catoffset'])){
       $current = $_GET['catoffset'];
     }else{
       $current = 1;
     }
     
     $html = '';
     $html .= '<div class="woo-xml-pagination">';
     
     if($pages != 1){
     
      for ($i=1; $i <= $pages; $i++){
        if($current == $i){
            $html .= '<span class="btn btn-default">'.$i.'</span>';
        }else{
            $html .= '<a class="btn btn-primary" href="'.admin_url().'admin.php?page='.$page.'&catoffset='.$i.'">'.$i.'</a>';
        }
      }
     
     }
     
     $html .= '</div>';
     
     return $html;
  }  