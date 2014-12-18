<?php
class GameAction extends CommonAction{
	
	public function index(){
		$map=$this->_search();

        $order=$this->ordermap('id','asc');
        
        $model=M('member');
        $this->list=$list=$this->getlist($model,$map,$order);

        $this->display();
	}


	public function status(){
		$data=array(
			'id'=>I('id',intval),
			'status'=>I('type',intval)
			);
		
		if(!M('member')->save($data)){			
			$this->error('修改失败');
		}
		$this->redirect('index');
	}

	//
	public function update(){
		$this->vo=$vo=M('member')->find(I('id',intval));
		$this->display();
	}
	//
	public function del(){
		if(!M('member')->delete(I('id',intval))){
			$this->errpr('删除失败');
		}
		$this->redirect('index');
	}


	protected function _search(){
        $map=array();
        $username=I('k');
        $status=I('q');
        if($status>-1&&$status!=""){
            $map['status']=array('eq',$status);
        }
        
        $map['username']=array('like','%'.I('k').'%');
        $this->search=array(
            'k'=>$username,
            'q'=>$status
            );
        return $map;
    }

    /**
     * [getUserInfo 获取用户信息]
     * @return [type] [description]
     */
    public function getUserInfo(){
    	$this->import_class();
    	$csrptg = new CRequestGetUserDetailInfo();
         
    	set_time_limit(0);
        $site=C('BaseSite.game_sever.url');
        $se = new SocketEngine($site['url'], $site['point'], false);

        if (!$se->getFP()) {
            #echo "$errstr ($errno)<br />\n";
        } else {
            $csrptg = new CRequestGetUserDetailInfo();
            $csrptg->setUin(24);
            $csrptg->Encode();

            $requestContent = $csrptg->getBuffer();

            //组装头信息
            $csh = new CSHead();
            $csh->setNUIN(1);
            $csh->setShFlag(0);
            $csh->setNOptionalLen(0);
            $csh->setLpbyOptional('');
            $csh->setShMessageID(CS_MSG_GM_GET_USER_DETAIL_INFO);
            $csh->setShMessageType(MSG_TYPE_REQUEST);
            $csh->setShVersion('');
            $csh->setNPlayerID(24);
            $csh->setNSequence(65533);
            $csh->setNHeaderLen();
            $csh->setNPackageLength($csh->getNHeaderLen() + strlen($requestContent));
            $csh->Encode();


            $requestHead = chr(0) . $csh->getBuffer();
            $request = $requestHead . $requestContent;

 			//发送请求
            $se->sendData($request);

            if ($se->getPackageLength()) {
                $se->getAllData();
                $se->getHeadLength();
                //得到返回结果
                $aaaa = $se->getPackageContent('CResponseGetUserDetailInfo', 'Decode');
                p($aaaa->getBaseInfo());die;
            }

        }

    }



}