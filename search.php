<?php 
require('classes/SearchResultProvider.php');
require('config.php');

if(!empty($_GET['term'])) {
    $term = $_GET['term']; 
}
/*else {
    exit ("you must enter a search term");
}*/
 $type = isset($_GET["type"]) ? $_GET["type"] : "site";
 $page = isset($_GET["page"]) ? $_GET["page"] : 1;

?>

<!DOCTYPE html>
<html>

<head>
    <title>Doodle</title>
    <style>
        .header {
    background-color: #fafafa;
    border-bottom: 1px solid #ebebeb;
}
.mainResultSection {
    flex:1;
}
.mainResultSection .resultsCount {
    margin-left:150px;
    font-size:16px;
    color:#808080;
}
.logoContainer {
    width:150px;
    padding: 5px 20px;
}
.wrapper .headerContent {
    display: flex;
    align-items: center;
}
.searchBarContainer {
    flex:1;
    height:44px;
    border-radius: 2px;
    background-color: #fff;
    border: none;
    box-shadow: 0.5px 2px 2px 0 rgba(0, 0, 0, 0.16);
    width: 600px;
    height: 44px;
    
    box-sizing: border-box;
    outline: none;
    display: flex;
}
.searchBox {
    padding:10px;
    border:none;
    font-size: 20px;
    background-color: transparent;
    flex:1;
}
.searchBox:focus {
    border-color: white;
}
form {
    margin : 15px 0 28px 0;
}
button {
    background: transparent;
    border: none;
    
}
.tabsContainer {
    margin-left: 150px;
}

.tabsContainer .tabList {
    padding:0;
    margin:0;

} 

.resultTitle a {
    text-decoration:none;
    color:#1a0dab;
    font-family:sans-serif;
    font-weight:normal;
    font-2yjsize:18px;
}

.resultTitle a:hover {
    text-decoration:underline;
}

.resultTitle {
    margin-bottom:3px;
}


.resultContainer {
    display:flex;
    flex-direction:column;
    margin-left:150px;
}

.resultUrl {
    margin-top:3px;
    color:#006621;
}

.resultDescription {
    color:#808080;
    font-size:15px;
    text-justify:distribute;
    width:400px;
}
.tabsContainer .tabList li {
    display:inline-block;
    padding: 0 16px 12px 16px; 
    text-decoration:none;
    font-size:17px;
    font-weight:bold;
}
.tabsContainer .tabList li.active {
    border-bottom:3px solid #1A73E8 ;
    
}
.tabsContainer .tabList li.active a {
    font-weight:bold;
    color:#1A73E8;
    text-decoration : none;

}

.tabsContainer .tabList li a {
    text-decoration : none;
}
.error {
    margin-left:150px;
    font-size:25px;
}
button img {
    width:22px;
}

.paginationContainer {
    display: flex;
    justify-content:center;
    margin-bottom:25px;
}

.pageNumberContainer img {
    height:37px;
}

.paginationContainer .pageButtons {
    display:flex;
}

.pageNumberContainer {
    display:flex;
    flex-direction:column;
    align-items: center;
    text-decoration:none;
}

.pageNumber {
    color:#000;
    font-size:13px;
}

a .pageNumber {
    color:#4285f4;
}
</style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="headerContent">
                <div class="logoContainer">
                    <a href="index.php"><img width="100%" src="images/festisite_google.png" alt="doobleimage"></a>
                </div>
                <div class="searchContainer">
                    <form action="search.php" method="get">
                        <div class="searchBarContainer">
                            <input type="text" name="term" class="searchBox" value="<?php echo $term; ?>">
                            <button><img src="images/icons8-search-50.png"> </button> </div>
                    </form>
                </div>
            </div>
            <div class="tabsContainer">
                <ul class="tabList">
                    <li class='<?php echo $type == "site" ? "active" : ""; ?>'>
                        <a href='<?php echo "search.php?term=$term&type=site"; ?>'>Sites</a>
                    </li>
                    <li class='<?php echo $type == "images" ? "active" : ""; ?>'>
                        <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mainResultSection">
            <?php  
        if(empty($term))  die ("<p class='error'>you must provide a search term :(</p>");
        $resultsProvider = new SearchResultProvider($connection);
        $numResults = $resultsProvider->getResults($term);
        echo "<p class='resultsCount'>$numResults result(s) found</p>";
        echo $resultsProvider->getResultsHtml($page,10,$term);
        ?>
        </div>
        <div class="paginationContainer">
            <div class="pageButtons">

                <div class="pageNumberContainer">
                    <img src="images/pageStart.png">
                </div>
                <?php 
            $pagesToShow = 10;
            $numPages  = floor($numResults / 10);
            if($numResults %10 != 0) $numPages++;
            $currentPage = 1;
            
            while($numPages != 0) {

                if($currentPage == $page) {
                    echo "<div class='pageNumberContainer'>
                <img src='images/pageSelected.png'>
                    <span class='pageNumber'>$currentPage<span>
  
                      </div>";
                      $currentPage++;
                      $numPages--;
                }
                else {
                echo "<div class='pageNumberContainer'>
                    <a href='search.php?term=$term&type=site&page=$currentPage'>    
                        <img src='images/page.png'>
                    </a>
                    <span class='pageNumber'>$currentPage<span>
  
                      </div>";
                      $currentPage++;
                      $numPages--;
                }
            }
           
            ?>
                <div class="pageNumberContainer">
                    <img src="images/pageEnd.png">
                </div>
            </div>


        </div>
    </div>
</body>

</html>