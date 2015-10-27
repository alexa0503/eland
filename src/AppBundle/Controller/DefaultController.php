<?php
namespace AppBundle\Controller;

use AppBundle\Wechat;
use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Drawer;
use Imagine\Image\ImageInterface;
use Imagine\Image\FontInterface;
use Imagine\Image\Palette;
#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
	public function getUser()
	{
		$session = $this->get('session');
		//$em = $this->getDoctrine()->getManager();
		$user = $this->getDoctrine()->getRepository('AppBundle:WechatUser')->findOneByOpenId($session->get('open_id'));
		return $user;
	}
	/**
	 * @Route("/", name="_index")
	 * @Route("/m", name="_m_index")
	 * @Route("/pc", name="_pc_index")
	 */
	public function indexAction(Request $request)
	{
		if( null != $this->getUser()->getCover() ){
			return $this->redirect($this->generateUrl('_m_info'));
		}
		
		if($request->attributes->get('_route') !== '_pc_index')
			return $this->render('AppBundle:default:m/index.html.twig');
		else
			return $this->render('AppBundle:default:pc/index.html.twig');
	}
	/**
	 * @Route("/m/select/{step}/{gender}", name="_m_select")
	 * @Route("/pc/select/{step}/{gender}", name="_pc_select")
	 */
	public function selectAction(Request $request, $step=0, $gender = 0)
	{
		if( null != $this->getUser()->getCover() ){
			return $this->redirect($this->generateUrl('_m_info'));
		}

		if($step == 1 )
			return $this->render('AppBundle:default:m/select_cloth.html.twig');
		elseif($step == 2 )
			return $this->render('AppBundle:default:m/sign.html.twig');
		else
			return $this->render('AppBundle:default:m/select_gender.html.twig');
	}
	/**
	 * @Route("/m/info", name="_m_info")
	 * @Route("/pc/info", name="_pc_info")
	 */
	public function infoAction(Request $request)
	{
		if( null != $this->getUser()->getCover() && null != $this->getUser()->getCover()->getUsername() ){
			return $this->redirect($this->generateUrl('_m_share'));
		}
		return $this->render('AppBundle:default:m/info.html.twig');
	}
	/**
	 * @Route("/post", name="_post")
	 */
	public function postAction(Request $request)
	{
		if($request->getMethod() == "POST"){
			if(preg_match('/^1\d{10}$/', $request->get('mobile'))){
				$em = $this->getDoctrine()->getManager();
				$user = $this->getUser();
				$cover = $user->getCover();
				$cover->setUsername($request->get('username'));
				$cover->setMobile($request->get('mobile'));
				$em->persist($cover);
				$em->flush();
				$json = array(
					'ret'=>0,
					'msg'=>''
				);
			}
			else{
				$json = array(
					'ret'=>1002,
					'msg'=>'手机号码不符合规则'
				);
			}
		}
		else{
			$json = array(
				'ret'=>1001,
				'msg'=>'来源不正确'
			);
		}
		return new Response(json_encode($json));
	}
	/**
	 * @Route("/m/combine", name="_m_combine")
	 * @Route("/pc/combine", name="_pc_combine")
	 */
	public function combineAction(Request $request)
	{
		$json = array(
			'ret'=>0,
			'msg'=>''
		);
		if( null != $this->getUser()->getCover() ){
			$json = array(
				'ret'=>1001,
				'msg'=>'已经生成过封面了，无法再次生成'
			);
		}
		else{
			$img = $request->get('img');
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$sign_path = 'uploads/' . uniqid() . '.png';
			//$file1 = 'uploads/' . uniqid() . '.png';
			file_put_contents($sign_path, $data);
			$em = $this->getDoctrine()->getManager();
			$cover = new Entity\Cover;
			$user = $this->getUser();
			$cover->setUser($user);
			$cover->setImgUrl($sign_path);
			$cover->setStyle($request->get('cover'));
			$cover->setHairStyle($request->get('hair'));
			$cover->setCloth1($request->get('cloth1'));
			$cover->setCloth2($request->get('cloth2'));
			$cover->setUsername('');
			$cover->setMobile('');
			$cover->setCreateIp($request->getClientIp());
			$cover->setCreateTime(new \DateTime('now'));
			$em->persist($cover);
			$em->flush();
		}
		
		//$cover->save();
		/*
		$imagine = new Imagine();
		$options = array(
				'png_quality' => 100,
		);

		$img = $request->get('img');
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$sign_path = 'uploads/' . uniqid() . '.png';
		$file1 = 'uploads/' . uniqid() . '.png';
		file_put_contents($sign_path, $data);
		
		$cover_path = 'bundles/app/default/m/images/book'.$request->get('cover').'.jpg';
		$hair_path = 'bundles/app/default/m/images/girlF'.$request->get('hair').'.png';
		$cloth1_path = 'bundles/app/default/m/images/girlT'.$request->get('cloth1').'.png';
		$cloth2_path = 'bundles/app/default/m/images/girlB'.$request->get('cloth2').'.png';
		
		$options = array(
				'png_quality' => 100,
		);
		$collage = $imagine->create(new Box(506,1400));
		$cover = $imagine->open($cover_path);
		$hair = $imagine->open($hair_path);
		$cloth1 = $imagine->open($cloth1_path);
		$cloth2 = $imagine->open($cloth2_path);
		$sign = $imagine->open($sign_path);
		$sign->resize(new Box(128, 128))->save($sign_path, $options);
		$collage->paste($cover, new Point(0 , 0))
			->paste($hair, new Point(26 , 95))
			->paste($cloth1, new Point(26 , 95))
			->paste($cloth2, new Point(26 , 95))
			->paste($sign, new Point(365 , 240))
			->crop(new Point(0, 0), new Box(506, 696));
		$collage->save($file1,$options);
		$collage = $imagine->open($file1);
		//$palette = new RGB();
		$color = new \Imagine\Image\Color('#FFFFFF');
		$font = new \Imagine\Gd\Font('/Users/Alexa/website/eland/web/fonts/SIMHEI.TTF', 40, $color);
		$collage->draw()->text('搭配出属于我的', $font, new Point(20,426));
		$collage->save($file1,$options);
		*/
		
		return new Response(json_encode($json));
	}
	/**
	 * @Route("/m/tmall", name="_m_tmall")
	 * @Route("/pc/tmall", name="_pc_tmall")
	 */
	public function tmallAction(Request $request)
	{
		return $this->render('AppBundle:default:m/tmall.html.twig');
	}
	/**
	 * @Route("/m/share", name="_m_share")
	 * @Route("/pc/share", name="_pc_share")
	 */
	public function shareAction(Request $request)
	{
		$user = $this->getUser();
		$request->getSession()->set('wx_share_url', 'http://'.$request->getHost().$this->generateUrl('_m_vote', array(
	            'user_id' => $user->getId(),
	        )));
		$repository = $this->getDoctrine()->getRepository('AppBundle:Cover');
		$query = $repository->createQueryBuilder('a')
			->orderBy('a.favourNum', 'DESC')
			->setMaxResults(5)
			->getQuery();
		$covers = $query->getResult();

		$repository = $this->getDoctrine()->getRepository('AppBundle:Cover');
		$query = $repository->createQueryBuilder('a')
			->select('count(a)')
			->where('a.favourNum > :favourNum')
			->setParameter('favourNum', $user->getCOver()->getFavourNum())
			->getQuery();
		$ranking = $query->getSingleScalarResult();
		return $this->render('AppBundle:default:m/share.html.twig', array(
			'covers'=>$covers,
			'user'=>$user,
			'ranking'=>$ranking+1
		));
	}
	/**
	 * @Route("/m/vote/view/{id}", name="_m_vote")
	 * @Route("/pc/vote/view/{id}", name="_pc_vote")
	 */
	public function voteAction(Request $request, $id = null)
	{
		if( null == $id){
			return $this->redirect($this->generateUrl('_index'));
		}
		$user = $this->getDoctrine()->getRepository('AppBundle:WechatUser')->find($id);
		if( null == $user){
			return $this->redirect($this->generateUrl('_index'));
		}
		return $this->render('AppBundle:default:m/vote.html.twig', array(
			'user'=>$user,
		));
	}
	/**
	 * @Route("/m/vote/post/{id}", name="_m_vote_post")
	 * @Route("/pc/vote/post/{id}", name="_pc_vote_post")
	 */
	public function votePostAction(Request $request, $id = null)
	{
		$return = array(
			'ret'=>0,
			'msg'=>''
		);
		if( null == $id){
			$return = array(
				'ret'=>1001,
				'msg'=>'不存在该用户'
			);
		}
		else{
			$user = $this->getDoctrine()->getRepository('AppBundle:WechatUser')->find($id);
			if( null == $user || null == $user->getCover()){
				$return = array(
					'ret'=>1002,
					'msg'=>'不存在该用户'
				);
			}
			elseif($this->getUser() === $user){
				$return = array(
					'ret'=>1003,
					'msg'=>'不能给自己投票喔'
				);
			}
			else{
				$em = $this->getDoctrine()->getManager();
				$em->getConnection()->beginTransaction();
				try {
					$cover = $user->getCover();
					$cover->increaseNum();
					$log = new Entity\VoteLog;
					$log->setUser($user);
					$log->setVoter($this->getUser());
					$log->setCreateTime(new \DateTime("now"));
					$log->setCreateIp($request->getClientIp());
					$em->persist($cover);
					$em->persist($log);
					$em->flush();
					$em->getConnection()->commit();
				 } catch (Exception $e) {
			            // Rollback the failed transaction attempt
			            $em->getConnection()->rollback();
			            $return['ret'] = 1100;
			            $return['msg'] = $e->getMessage();
			        }
				
			}
		}
		return new Response(json_encode($return));
	}
	/**
	 * @Route("/wxSign", name="_wx_sign")
	 */
	public function wechatSignAction(Request $request)
	{
		if( null == $request->get('url')){
			return new Response('');
		}
		//$url = urldecode($request->get('url'));
		$appId = $this->container->getParameter('wechat_appid');
		$appSecret = $this->container->getParameter('wechat_secret');
		$wechat = new Wechat\Wechat($appId, $appSecret);
		$wx = (array)$wechat->getSignPackage(urldecode($request->get('url')));
		//var_dump($wx);
		$wx['shareTitle'] = '分享文案';
		$wx['shareDesc'] = '分享文案';
		$wx['shareUrl'] = 'http://'.$request->getHost().'/';
		$wx['imgUrl'] = 'http://'.$request->getHost().'/images/share.jpg';
		return new Response(json_encode($wx));
	}

	/**
	 * @Route("callback/", name="_callback")
	 */
	public function callbackAction(Request $request)
	{
		$session = $request->getSession();
		$code = $request->query->get('code');
		//$state = $request->query->get('state');
		$app_id = $this->container->getParameter('wechat_appid');
		$secret = $this->container->getParameter('wechat_secret');
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $app_id . "&secret=" . $secret . "&code=$code&grant_type=authorization_code";
		$data = Helper\HttpClient::get($url);
		$token = json_decode($data);
		//$session->set('open_id', null);
		if ( isset($token->errcode) && $token->errcode != 0) {
			return new Response('something bad !');
		}

		$wechat_token = $token->access_token;
		$wechat_openid = $token->openid;
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$wechat_token}&openid={$wechat_openid}";
		$data = Helper\HttpClient::get($url);
		$user_data = json_decode($data);

		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
			$session->set('open_id', $user_data->openid);
			$repo = $em->getRepository('AppBundle:WechatUser');
			$qb = $repo->createQueryBuilder('a');
			$qb->select('COUNT(a)');
			$qb->where('a.openId = :openId');
			$qb->setParameter('openId', $user_data->openid);
			$count = $qb->getQuery()->getSingleScalarResult();
			if($count <= 0){
				$wechat_user = new Entity\WechatUser();
				$wechat_user->setOpenId($wechat_openid);
				$wechat_user->setNickName($user_data->nickname);
				$wechat_user->setCity($user_data->city);
				$wechat_user->setGender($user_data->sex);
				$wechat_user->setProvince($user_data->province);
				$wechat_user->setCountry($user_data->country);
				$wechat_user->setHeadImg($user_data->headimgurl);
				$wechat_user->setCreateIp($request->getClientIp());
				$wechat_user->setCreateTime(new \DateTime('now'));
				$em->persist($wechat_user);
				$em->flush();
			}
			else{
				$wechat_user = $em->getRepository('AppBundle:WechatUser')->findOneBy(array('openId' => $wechat_openid));
				$wechat_user->setHeadImg($user_data->headimgurl);
				$em->persist($wechat_user);
				$em->flush();
				$session->set('user_id', $wechat_user->getId());
			}

			$redirect_url = $session->get('redirect_url') == null ? $this->generateUrl('_index') : $session->get('redirect_url');
			$em->getConnection()->commit();
			return $this->redirect($redirect_url);
		}
		catch (Exception $e) {
			$em->getConnection()->rollback();
			return new Response($e->getMessage());
		}
	}
}
