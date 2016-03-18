<?php

/**
 * This is the model class for table "lifeexpectancy".
 *
 */
class Paginator extends CPagination {

    public function __construct($counts) {
        $this->setItemCount($counts);
    }

    /**
     * @return string the associated database table name
     */
    public function paginatorHtml() {
        $str = '';
        $pageCount = $this->getPageCount();
        if ($pageCount > 0) {
            $previous_page = $this->getCurrentPage() - 1;
            $next_page = $this->getCurrentPage() + 1;
            $str = '<ul class="paginationUL">';
            $j = 0;
            $class = 'class ="pagelink"';
            $selectRec = '<span class="rppDD1" style="display:none"><li><a style="font-weight:normal;">Records / Page:</a></li><li>                          
                                <div style="z-index:1" class="sortDD">
                                <button id="recordsButton" class="recButton" style="width:50px;margin:0 5px !important;padding:2px 0 !important;" data-toggle="dropdown">
                                <span class="recordsButtonSpan">25</span>
                                <span class="caret" style="float: right; margin: 5px; display: block;"> </span>
                                </button>
                                <ul style="min-width:45px;top:25px;right:2px;" class="dropdown-menu">';
            for ($i = 25; $i <= 100; $i = $i + 25) {

                $selectRec .= '<li>                                                                                              
                                        <a href="javascript:void(0);" class="recordspageRange recordlink" recordscount="' . $i . '">' . $i . '</a> 
                                    </li>';
            }

            $selectRec .= '</ul></div></li></span>';
            $str .=$selectRec;
            if ($pageCount > 1) {
                //$str .="<li><a pageno='1' class='pagelink'  href='javascript:void(0)'> First </a></li>";
                if ($this->getCurrentPage() > 1) {
                    $str .="<li><a pageno='1' class='pagelink'  href='javascript:void(0)'> First </a></li>";
                    $str .="<li><a class='pagelink' href='javascript:void(0)' pageno='" . $previous_page . "'> Prev </a></li>";
                } else {
                    $str .="<li><a href='javascript:void(0)'style='font-weignt:normal'> First </a></li>";
                    $str .="<li><a href='javascript:void(0)' > Prev </a></li>";
                }

                $select = '<li>                          
                                <div style="z-index:1" class="sortDD">
                                <button id="milesButton" class="recButton" style="width:40px;margin:0 5px !important;padding:2px 0 !important;" data-toggle="dropdown">
                                <span id="milesButtonSpan">' . $this->getCurrentPage() . '</span>
                                <span class="caret" style="float: right; margin: 5px; display: block;"> </span>
                                </button>
                                <ul style="min-width:45px;top:25px;right:2px;" class="dropdown-menu">';
                for ($i = 1; $i <= $pageCount; $i++) {

                    $select .= '<li>                                                                                              
                                        <a href="javascript:void(0);" class="milesDateRange pagelink" pageno="' . $i . '">' . $i . '</a> 
                                    </li>';
                }

                $select .= '</ul>
                                </div>
                                </li>';
//				$select = "<select name='pagelink_drop1' class='pagelink_drop span1' style='float:left;'>";
//				for($i = 1; $i <= $pageCount ; $i++) {
//					if ($i == $this->getCurrentPage())
//						$select .= "<option selected ='selected' value= '" . $i ."'>$i</option>";
//					else
//						$select .= "<option value= '" . $i ."'>$i</option>";
//				}
//				$select .='</select>';
                $str .=$select;
                if ($this->getCurrentPage() == $pageCount) {
                    $str .="<li><a href='javascript:void(0)'> Next </a></li>";
                    $str .="<li><a href='javascript:void(0)'> Last </a></li>";
                } else {
                    $str .="<li><a  class='pagelink' pageno='" . $next_page . "' href='javascript:void(0)'> Next </a></li>";
                    $str .="<li><a pageno = '" . $pageCount . "' class='pagelink' href='javascript:void(0)'> Last </a></li>";
                }
                //$str .="<li><a pageno = '" . $pageCount ."' class='pagelink' href='javascript:void(0)'> Last </a></li>";
            } else {
                for ($i = 0; $i < $pageCount; $i++) {
                    $j++;
                    if ($this->getCurrentPage() == $j)
                        $class = 'class = " pagelink current"';
                    else
                        $class = 'class = " pagelink "';
                    $str .="<li><a pageno='" . $j . "' $class href='javascript:void(0)'> $j </a></li>";
                }
            }
            $str .='</ul>';
        }

        return $str;
    }

}

?>