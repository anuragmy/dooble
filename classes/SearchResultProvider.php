<?php


class SearchResultProvider {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function getResults($term) {
        $term = "%".$term."%";
        $sql = "select count(*) as total from sites where title like '$term' or url like '$term'  ";
        $result = mysqli_query($this->connection,$sql);
        if ($result && mysqli_num_rows($result) >= 1 ) {
            $rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $rows["total"];
        }
    }

    public function getResultsHtml($page,$pageSize,$term) {
        $pageLimiter = ($page - 1) * $pageSize; 
        $results = "";
        $term = "%".$term."%";
        $sql = "select * from sites where title like '$term' or url like '$term' order by clicks desc limit $pageLimiter,$pageSize";
        $result = mysqli_query($this->connection,$sql);

        if ($result && mysqli_num_rows($result) >= 1 ) {
            while($rows = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                    $title = $rows['title'];
                    $title = trim($title,55);
                    $url = $rows['url'];
                    $description = $rows['description'];
                    $description = trim($description,55);


                    $results .= "<div class='resultContainer'>
                                <h3 class='resultTitle'>
                                <a href='$url'>$title</a>
                                </h3>
                                <span class='resultUrl'>$url</span>
                                <span class='resultDescription'>$description</span>
                                
                                </div>";
            }
        }

        return $results;
    }

    private function trim($string,$characterLimit) {

        $dots = (strlen($string) > $characterLimit ? '...':'');
        return $string = strpos($string,0,$characterLimit) .$dots;
    }
}

