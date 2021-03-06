<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
require_once("class.AbstractBaseClass.php");

class Spieler extends AbstractBaseClass
{
    protected static $columns=array("id","vorname","nachname","geburtsdatum");
    private $id;
    private $vorname;
    private $nachname;
    private $geburtsdatum;
    
    public function __construct($id=0)
    {
        $this->id=$id;
        if($id!=0)
        {
            $this->load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT * FROM spieler WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $result=$stmt->fetch();
            if($result)
            {
                $this->vorname=$result["vorname"];
                $this->nachname=$result["nachname"];
                $this->geburtsdatum=$result["geburtsdatum"];
            }
            else
            {
                $error.="Leeres Resultat";
            }
        }
        else
        {
            $error.=$stmt->errorInfo()[2];
        }
        if(strlen($error))
        {
            throw new Exception($error);
        }
    }
    public function update()
    {
        static::initDB();
        $insert="INSERT INTO spieler (id,vorname,nachname,geburtsdatum) VALUES (:id,:vorname,:nachname,:geburtsdatum)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE vorname=:vorname ,nachname=:nachname ,geburtsdatum=:geburtsdatum");
            
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":vorname",$this->vorname);
        $stmt->bindValue(":nachname",$this->nachname);
        $stmt->bindValue(":geburtsdatum",$this->geburtsdatum);
                
        if(!$stmt->execute())
        {
            throw new Exception($stmt->errorInfo()[2]);
        }        
    }
    public function get_as_array()
    {
        return array("Id" => $this->getId(),
                     "vorname" => $this->getVorname(),
                     "nachname" => $this->getNachname(),
                     "geburtsdatum" => $this->getGeburtsdatum());
    }
    public function setValues($id,$vorname,$nachname,$geburtsdatum)
    {
        $this->setId($id);
        $this->setVorname($vorname);
        $this->setNachname($nachname);
        $this->setGeburtsdatum($geburtsdatum);
    }
    public function getId()
    {
        return $this->id;
    }
    public function getVorname()
    {
        return $this->vorname;
    }
    public function getNachname()
    {
        return $this->nachname;
    }
    public function getGeburtsdatum()
    {
        return $this->geburtsdatum;
    }
        
    public function setId($id)
    {
        $this->id=$id;
    }
    public function setVorname($vorname)
    {
        if(strlen($vorname))
        {
            $this->vorname=$vorname;
        }
    }
    public function setNachname($nachname)
    {
        $this->nachname=$nachname;
    }
    public function setGeburtsdatum($geburtsdatum)
    {
        $this->geburtsdatum=$geburtsdatum;
    }
    public function getName()
    {
        return $this->vorname." ".$this->nachname;
    }

    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT * FROM spieler");
            $error="";
            static::$all_elements=array();
            if($stmt->execute())
            {                
                $joinarray=static::getJoinArray($stmt,Spieler::getColumns("spieler"));
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $spieler_temp=new Spieler();
                    print_r($joinarray);
                    $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                    echo($spieler_temp->getId()." ".$spieler_temp->getName()." ");

                    array_push(static::$all_elements,$spieler_temp);
                }

            }
        }
        foreach(static::$all_elements as $spieler_temp)
        {
            echo($spieler_temp->getId()." ".$spieler_temp->getName()." ");
        }
        return static::$all_elements;
    }
}
