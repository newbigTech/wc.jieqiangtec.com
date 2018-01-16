<?php
class MDB{
    private static $_instance;  
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance  = new self();
        }
        return self::$_instance;
    }
    private function __construct(){}
    private function __clone(){}


    /**
     * [insert 往数据库插入一条新的数据，模块下的表使用]
     * @param  [string]  $_table [数据表名]
     * @param  array   $_data  [要插入的数据]
     * @param  boolean $_whereTable  [判断是哪里的数据表，默认为false 模块下的数据表，true等于微擎官方的数据表]
     * @param  boolean $_type  [开启自动判断主见是否存在，如果存在修改，不存在新增  默认不开启false  开启true]
     * @return [boolean]          [返回是否新增成功]
     */
    public function insert($_table,$_data = array(),$_whereTable = false ,$_type = false){
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;
        return pdo_insert($_table,$_data,$_type); 
    }

    
    
    /**
     * [updateMath 修改数据加或者减] 
     * @param  [string] $_table     [数据表名]
     * @param  array  $_data      [修改的数据]
     * @param  array  $_condition [查询条件]
     * @param  bool $_whereTable [查询当前模块表还是官方表 默认false查询当前模块表 true官方表]
     * @return [bool]             [返回执行结果]
     * //例如  加  $_data = array('num'=>'++5++')   指定字段加5
     *  例如  减  $_data = array('num'=>'--5--')   指定字段减5
     */
    public function updateMath($_table,$_data = array(),$_condition = array(),$_whereTable = false){
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;
        foreach($_data as $_key=>$_value){
            if(substr($_value,0,2) == '++' && substr($_value,-2) == '++'){
              $_newData[] = "`".$_key."`=`".$_key."`+'".substr(substr($_value,2,20),0,-2)."'";
            }else if(substr($_value,0,2) == '--' && substr($_value,-2) == '--'){
              $_newData[] = "`".$_key."`=`".$_key."`-'".substr(substr($_value,2,20),0,-2)."'";
            }else{
               $_newData[] = "`".$_key."`='".$_value."'";
            }         
        }
        foreach($_condition as $_key=>$_value){
            $_newCon[] = "`".$_key."`='".$_value."'";
        }
        $_stringCon = implode(' AND ',$_newCon);
        $_stringData = implode(',',$_newData);

        $_sql = "UPDATE "
                                        .tablename($_table)."
                              SET "
                                        .$_stringData." 
                        WHERE "
                                        .$_stringCon;
      return pdo_query($_sql);
    }
    

    
    /**
     * [update 修改一条数据]
     * @param  [string]  $_table      [数据表名]
     * @param  array   $_data       [修改的数据]
     * @param  array   $condition   [查询条件]
     * @param  boolean $_whereTable [当前模块下的表=false默认 || 官方表=true]
     * @param  string  $glue        [条件类型 默认AND]
     * @return [type]               [返回执行结果]
     */
    public function update($_table,$_data = array(),$condition = array(),$_whereTable = false,$glue = 'AND'){
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;
        return pdo_update($_table,$_data,$condition,$glue);
    }
    
    
    
    /**
     * [selectOne 查询一条数据]
     * @param  [string]  $_table      [数据表名]
     * @param  array   $_data       [查询条件]
     * @param  array   $_fields     [返回的字段]
     * @param  boolean $_whereTable [当前模块下的表=false默认 || 官方表=true]
     * @return [array]               [返回结果数组]
     */
    public function selectOne($_table,$_data = array(),$_fields = array(),$_whereTable = false){
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;
        return pdo_get($_table,$_data,$_fields);
    }

    

    /**
     * [selectAll 查询多条数据]
     * @param  [string]  $_table    [数据表名]
     * @param  [type]  $_where      [查询条件]
     * @param  array   $_rests      [查询其他 ORDER BY AND LIMIT ]
     * @param  array   $_return     [返回字段]
     * @param  boolean $_whereTable [当前模块下的表=false默认 || 官方表=true]
     * @return [array]              [查询结果，返回数组]
     */
    public function selectAll($_table,$_where,$_rests = array(),$_return = array(),$_whereTable = false,$_or = array()){
        if(is_array($_return) && count($_return) > 0){
            $_newReturn = implode(',',$_return);
        }else{
            $_newReturn = '*';
        }
        
        if(is_array($_where) && count($_where) >0){
            foreach($_where as $_key=>$_value){
                if(!!$_temp = count(explode(' ',$_key))==2){
                    $_newWhere[] = $_key.$_temp[1]. "'".$_value."'";
                }else{
                    $_newWhere[] = $_key.' = '.$_value;
                }
            }
            $_newWhere = implode(' AND ',$_newWhere);
        }
        
        //或者  OR
        if(is_array($_or) && count($_or) > 0){
            foreach($_or as $_key=>$_value){
                if(!!$_temp = count(explode(' ',$_key))==2){
                    $_newOr[] = $_key.$_temp[1]. "'".$_value."'";
                }else{
                    $_newOr[] = $_key.' = '.$_value;
                }
            }
            if($_newWhere != ''){
                $_newWhere .=' OR '.implode(' OR ',$_newOr);
            }else{
                $_newWhere = ' WHERE '.implode(' OR ',$_newWhere);
            }
        }
        
        if(is_array($_rests) && count($_rests) > 0){
            $_order = isset($_rests['order']) ? $_rests['order'] : '';
            $_limit = isset($_rests['limit']) ? $_rests['limit'] : '';
        }
        
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;

        $_sql = "SELECT 
                                    $_newReturn 
                      FROM "
                                    .tablename($_table).
                                  "
                     WHERE
                                     $_newWhere
                                     $_order
                                     $_limit
                     ";
        return pdo_fetchall($_sql);
    }


    
    //返回总记录
    /**
     * [total 返回总记录数]
     * @param  [type]  $_table      [数据表名字]
     * @param  array   $_where      [查询条件]
     * @param  boolean $_whereTable [当前模块下的表=false默认 || 官方表=true]
     * @return [int]               [返回记录条数]
     */
    public function total($_table,$_where = array(),$_whereTable = false){
        if(is_array($_where) && count($_where) >0){
            foreach($_where as $_key=>$_value){
                if(!!$_temp = count(explode(' ',$_key))==2){
                    $_newWhere[] = $_key.$_temp[1]. "'".$_value."'";
                }else{
                    $_newWhere[] = $_key.' = '.$_value;
                }         
            }
            $_newWhere = ' WHERE '.implode(' AND ',$_newWhere);
        }else{
            $_newWhere = '';
        }
        
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;

        $_sql ="SELECT 
                                    COUNT(*) as count
                         FROM 
                                    ".tablename($_table)."
                                     $_newWhere
                    ";
       $_temp = pdo_fetchAll($_sql);
       return $_temp[0]['count'];
    }


    
    
    //返回总数  
    /**
     * [getSum 返回数据表某个字段的总和]
     * @param  [string] $_table [数据表名字]
     * @param  [type] $_field [查询综合的字段]
     * @param  array  $_where [查询条件]
     * @param  array  $_whereTable [当前模块下的表=false默认 || 官方表=true]
     * @return [type]         [description]
     */
    public function getSum($_table,$_field,$_where = array(),$_whereTable = false){
    
        if(is_array($_where) && count($_where) >0){
            foreach($_where as $_key=>$_value){
                if(!!$_temp = count(explode(' ',$_key))==2){
                    $_newWhere[] = $_key.$_temp[1]. "'".$_value."'";
                }else{
                    $_newWhere[] = $_key.' = '.$_value;
                }
            }
            $_newWhere = ' WHERE '.implode(' AND ',$_newWhere);
        }else{
            $_newWhere = '';
        }
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;
        $_sql ="SELECT
        SUM($_field) as sum
        FROM
        ".tablename($_table)."
        $_newWhere
        ";  
        $_temp = pdo_fetchAll($_sql);
        return $_temp[0]['sum'];
    }
  
    


    /**
     * [joinLeft 双表查询]
     * @param  array $_tables [查询表的数组  下标0为主表  下标1为左表]
     * @param  array $_on     [两个表的关联条件]
     * @param  array  $_where  [查询条件 AND]
     * @param  array  $_rests  [其他调价   ORDER AND LIMIT ]
     * @param  array  $_return [返回字段]
     * @param  array  $_or     [查询添加  OR]
     * @return [array]          [返回查询结果数组]
     */
    public function joinLeft(Array $_tables,Array $_on,Array $_where=array(),Array $_rests=array(),Array $_return = array(),Array $_or = array()){
        if(is_array($_on) && count($_on) >0){
            $_newOn = implode('=',$_on);
        }
    
       //并且  AND
    if(is_array($_where) && count($_where) >0){
            foreach($_where as $_key=>$_value){
                if(!!$_temp = count(explode(' ',$_key))==2){
                    $_newWhere[] = $_key.$_temp[1]. "'".$_value."'";
                }else{
                    $_newWhere[] = $_key.' = '.$_value;
                }
            }
            $_newWhere = ' WHERE '.implode(' AND ',$_newWhere);
        }else{
            $_newWhere = '';
        }
        
        //或者  OR
        if(is_array($_or) && count($_or) > 0){
            foreach($_or as $_key=>$_value){
                if(!!$_temp = count(explode(' ',$_key))==2){
                    $_newOr[] = $_key.$_temp[1]. "'".$_value."'";
                }else{
                    $_newOr[] = $_key.' = '.$_value;
                }
            }
            if($_newWhere != ''){
                $_newWhere .=' OR '.implode(' OR ',$_newOr);
            }else{
                $_newWhere = ' WHERE '.implode(' OR ',$_newWhere);
            }
        }
        
        if(is_array($_return) && count($_return) > 0){
            $_newReturn = implode(',',$_return);
        }else{
            $_newReturn = 'a.*,b.*';
        }
        
        
        if(is_array($_rests) && count($_rests) > 0){
            $_order = isset($_rests['order']) ? $_rests['order'] : '';
            $_limit = isset($_rests['limit']) ? $_rests['limit'] : '';
        }
        
        
        
        $_sql = "SELECT 
                                   $_newReturn 
                        FROM
                                ".tablename(LOUIE_PREFIX.$_tables[0])." a
                        LEFT JOIN
                                 ".tablename(LOUIE_PREFIX.$_tables[1])." b
                            ON
                                    $_newOn
                                    $_newWhere
                                    $_order
                                    $_limit
                    ";
       return pdo_fetchall($_sql);
    }
    
    

    //多表查询，功能更强，包括三表查询（待完善）
    /**
     * [joinLeftRight 三表查询]
     * @param  Array       $_tables [查询的数据表 下标0=主  下标1=左  下标2=右]
     * @param  Array       $_on     [数据表连接条件]
     * @param  Array|array $_where  [查询条件]
     * @param  Array|array $_rests  [其他条件 ORDER AND LIMIT]
     * @param  Array|array $_return [返回字段]
     * @return Array              [返回查询结果数组]
     */
    public function joinLeftRight(Array $_tables,Array $_on,Array $_where = array(),Array $_rests=array(),Array $_return = array()){
            if(count($_on) == 4){
                    $_newOn[0]= $_on[0].'='.$_on[1];
                    $_newOn[1] = $_on[2].'='.$_on[3];
            }else{
                exit('多表查询$_on参数错误');
            } 
            
           if(count($_where) > 0){
               foreach($_where as $_key=>$_value){
                   $_newWhere[] = $_key.' = '.$_value;
               }
               $_newWhere =' WHERE '.implode(' AND ',$_newWhere);
           }       
          
         if(count($_rests)){
             $_order = isset($_rests['order']) ? $_rests['order'] : '';
             $_limit = isset($_rests['limit']) ? $_rests['limit'] : '';
         }
   
         if(count($_return) > 0){
             $_newReturn = implode(',',$_return);
         }else{
             $_newReturn = 'a.*,b.*,c.*';
         }

        
            $_sql = "SELECT 
                                    $_newReturn
                       FROM
                                   ".tablename(LOUIE_PREFIX.$_tables[0])." a
                     LEFT JOIN
                                   ".tablename(LOUIE_PREFIX.$_tables[1])." b
                          ON
                                    $_newOn[0]
                     RIGHT JOIN
                                       ".tablename(LOUIE_PREFIX.$_tables[2])." c
                          ON
                                    $_newOn[1]
                                    $_newWhere
                                    $_order
                                    $_limit
                       ";  
       return pdo_fetchall($_sql);
    }

    
    
    //删除一条数据
    /**
     * [delete 删除一条数据]
     * @param  string  $_table      数据表名字
     * @param  array   $condition   删除条件
     * @param  string  $glue        条件连接 AND OR
     * @param  boolean $_whereTable [当前模块下的表=false默认 || 官方表=true]
     * @return boolean               返回删除结果
     */
    public function delete($_table,$condition = array(),$glue='AND',$_whereTable = false){
        $_table = $_whereTable ? $_table : LOUIE_PREFIX.$_table;
        return pdo_delete($_table,$condition,$glue);
    }
    
    
    
    //查询一条数据，多功能
    // public function selectOneKind($_table,$_where = array(),$_return = array(),$_order =''){
    //     if(is_array($_return) && count($_return)){
    //         $_newReturn = implode(',',$_return);    
    //     }else{
    //         $_newReturn = '*';
    //     }
        
    //     if(is_array($_where) && count($_where) != 0){
    //         foreach($_where as $_key=>$_value){
    //             $_temp[] = $_key .'='. $_value;
    //         }
    //         $_newWhere = 'WHERE '.implode(' AND ',$_temp);
    //     }
    //     $_sql = "SELECT 
    //                                 $_newReturn 
    //                     FROM ".
    //                                     tablename(LOUIE_PREFIX.$_table)."
    //                     $_newWhere
    //                     $_order
    //                    LIMIT 
    //                                         1
    //                   ";
    //     return pdo_fetch($_sql);
    // }
    
    
    

  
    
    
    
    
    
    
}