<?php
namespace AppBundle\Controller;

use AppBundle\Wechat;
use Imagine\Gd\Imagine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;

#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="_index")
	 */
	public function indexAction()
	{
		//return $this->redirect('index.html');
		return $this->render('AppBundle:default:m/index.html.twig');
	}
	/**
	 * @Route("/sign", name="_sign")
	 */
	public function signAction(Request $request)
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
		$wx['shareTitle'] = '讨厌！说好今晚疼人家的！';
		$wx['shareDesc'] = '你最疼的人是我，怎么舍得我今晚难受！';
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
