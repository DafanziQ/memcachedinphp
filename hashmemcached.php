<?php
/*crc32() 函数计算字符串的 32 位 CRC（循环冗余校验）*/
/*consisitenthash*/
interface hasher{
	public function _hash($str);
}
interface distribution{
	public function lookup($key);
}
class Consistent implements hasher,distribution{
	protected $_nodes=array();
	protected $_position=array();
	protected $mul=64;//每个结点对应64个虚结点
	public function _hash($str){
		return sprintf("%u",crc32($str));//把字符串转成32位无符号整数
	}
	/*核心功能*/
	public function lookup($key){
       $point=$this->_hash($key);
       //$node=current($this->_nodes);//先取圆环上最小的一个结点当成结果
       $viturepoint=current($this->_position);
       //foreach ($this->_nodes as $key => $value) {
       foreach ($this->_position as $key => $value) {
       	if($point<=$key){
       		$node=$value;
       	    break;
       	}
        }
        return $node;

	}
	public function addNode($node){
		for($i=0;$i<$this->mul;$i++){
            $this->_position[$this->_hash($node.'_'.$i)]=$node;    
        }
		 //$this->_nodes[$this->_hash($node)]=$node;//如array(13亿=>A,8亿=>B,24亿=>C)不可取,应该按顺序给数组按键排序
         //$this->sortNode();
         $this->sortPosition();	
	}
	//循环所有结点的虚位置，并将其删掉
	public function delNode($node){
		for($i=0;$i<$this->mul;$i++){
            unset($this->_position[$this->hash($node."_".$i)]);
		}
	}
    protected function sortNode(){
    	ksort($this->_nodes,SORT_REGULAR);
    }
    protected function sortPosition(){
    	ksort($this->_position,SORT_REGULAR);
    }
    //调试用的函数
    public function getNodes(){
    	print_r($this->_nodes);
    }
    public function getPosition(){
    	print_r($this->_position);
    }
}
$con=new Consistent();
$con->addNode('a');
$con->addNode('b');
$con->addNode('c');
echo "所有的服务器如下：<br/>";
//$con->getNodes();
$con->getPosition();
echo "<br/>当前键计算的hash落点是".$con->_hash("school")."<br/>";
echo $con->lookup("name");
/*
*以上观察可发现实际hash分布不均匀
另外没有虚拟结点，导致一个服务器down了，就全压在下一个结点了
*/
?>
