<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? Loader::library('authentication/open_id');?>
<? $form = Loader::helper('form'); ?>

<script type="text/javascript">
$(function() {
	$("input[name=uName]").focus();
});
</script>

<? if (isset($intro_msg)) { ?>
<div class="alert-message block-message success"><p><?=$intro_msg?></p></div>
<? } ?>

<div class="page-header">
	<h1><?=t('Sign in to %s', SITE)?></h1>
</div>

<? if( $passwordChanged ){ ?>

	<div class="block-message info alert-message"><p><?=t('Password changed.  Please login to continue. ') ?></p></div>

<? } ?> 

<? if($changePasswordForm){ ?>

	<p><?=t('Enter your new password below.') ?></p>

	<div class="ccm-form">	

	<form method="post" action="<?=$this->url( '/login', 'change_password', $uHash )?>"> 

		<div class="clearfix">
		<label for="uPassword"><?=t('New Password')?></label>
		<div class="input">
			<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
		</div>
		</div>
		<div class="clearfix">
		<label for="uPasswordConfirm"><?=t('Confirm Password')?></label>
		<div class="input">
			<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="ccm-input-text">
		</div>
		</div>

		<div class="actions">
		<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
		</div>
	</form>
	
	</div>

<? }elseif($validated) { ?>

<h3><?=t('Email Address Verified')?></h3>

<div class="success alert-message block-message">
<p>
<?=t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>
</p>
<div class="alert-actions"><a class="btn small" href="<?=$this->url('/')?>"><?=t('Continue to Site')?></a></div>
</div>


<? } else if (isset($_SESSION['uOpenIDError']) && isset($_SESSION['uOpenIDRequested'])) { ?>

<div class="ccm-form">

<? switch($_SESSION['uOpenIDError']) {
	case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE: ?>

		<form method="post" action="<?=$this->url('/login', 'complete_openid_email')?>">
			<p><?=t('To complete the signup process, you must provide a valid email address.')?></p>
			<label for="uEmail"><?=t('Email Address')?></label><br/>
			<?=$form->text('uEmail')?>
				
			<div class="ccm-button">
			<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
			</div>
		</form>

	<? break;
	case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
	
	$ui = UserInfo::getByID($_SESSION['uOpenIDExistingUser']);
	
	?>

		<form method="post" action="<?=$this->url('/login', 'do_login')?>">
			<p><?=t('The OpenID account returned an email address already registered on this site. To join this OpenID to the existing user account, login below:')?></p>
			<label for="uEmail"><?=t('Email Address')?></label><br/>
			<div><strong><?=$ui->getUserEmail()?></strong></div>
			<br/>
			
			<div>
			<label for="uName"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
				<?=t('Email Address')?>
			<? } else { ?>
				<?=t('Username')?>
			<? } ?></label><br/>
			<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
			</div>			<div>

			<label for="uPassword"><?=t('Password')?></label><br/>
			<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
			</div>

			<div class="ccm-button">
			<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
			</div>
		</form>

	<? break;

	}
?>

</div>

<? } else if ($invalidRegistrationFields == true) { ?>

<div class="ccm-form">

	<p><?=t('You must provide the following information before you may login.')?></p>
	
<form method="post" action="<?=$this->url('/login', 'do_login')?>">
	<? 
	$attribs = UserAttributeKey::getRegistrationList();
	$af = Loader::helper('form/attribute');
	
	$i = 0;
	foreach($unfilledAttributes as $ak) { 
		if ($i > 0) { 
			print '<br/><br/>';
		}
		print $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());	
		$i++;
	}
	?>
	
	<?=$form->hidden('uName', $_POST['uName'])?>
	<?=$form->hidden('uPassword', $_POST['uPassword'])?>
	<?=$form->hidden('uOpenID', $uOpenID)?>
	<?=$form->hidden('completePartialProfile', true)?>

	<div class="ccm-button">
		<?=$form->submit('submit', t('Sign In'))?>
		<?=$form->hidden('rcID', $rcID); ?>
	</div>
	
</form>
</div>	

<? } else { ?>

<form method="post" action="<?=$this->url('/login', 'do_login')?>">

<div class="row">
<div class="span8 columns">

<fieldset>
	
	<legend><?=t('User Account')?></legend>

	<div class="clearfix">
	
	<label for="uName"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
		<?=t('Email Address')?>
	<? } else { ?>
		<?=t('Username')?>
	<? } ?></label>
	<div class="input">
		<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
	</div>
	
	</div>
	<div class="clearfix">

	<label for="uPassword"><?=t('Password')?></label>
	
	<div class="input">
		<input type="password" name="uPassword" id="uPassword" class="ccm-input-text" />
	</div>
	
	</div>
</fieldset>

<? if (OpenIDAuth::isEnabled()) { ?>
	<fieldset>

	<legend><?=t('OpenID')?></legend>

	<div class="clearfix">
		<label for="uOpenID"><?=t('Login with OpenID')?>:</label>
		<div class="input">
			<input type="text" name="uOpenID" id="uOpenID" <?= (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
		</div>
	</div>
	</fieldset>
<? } ?>

</div>
<div class="span8 columns">

	<fieldset>

	<legend><?=t('Options')?></legend>

	<? if (isset($locales) && is_array($locales) && count($locales) > 0) { ?>
		<div class="clearfix">
			<label for="USER_LOCALE"><?=t('Language')?></label>
			<div class="input"><?=$form->select('USER_LOCALE', $locales)?></div>
		</div>
	<? } ?>
	
	<div class="clearfix">
		<label for="uMaintainLogin"><?=t('Remember Me')?></label>
		<div class="input">
		<ul class="inputs-list">
			<li><label><?=$form->checkbox('uMaintainLogin', 1)?> <span><?=t('Remain logged in to website.')?></span></label></li>
		</ul>
		</div>
	</div>
	
	
	<? $rcID = isset($_REQUEST['rcID']) ? preg_replace('/<|>/', '', $_REQUEST['rcID']) : $rcID; ?>
	<input type="hidden" name="rcID" value="<?=$rcID?>" />
	
	</fieldset>
</div>
<div class="span16 columns">
	<div class="actions">
	<?=$form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary'))?>
	</div>
</div>
</div>
</form>

<h3><?=t('Forgot Your Password?')?></h3>

<p><?=t("If you've forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.")?></p>

<a name="forgot_password"></a>

<form method="post" action="<?=$this->url('/login', 'forgot_password')?>">
<input type="hidden" name="rcID" value="<?=$rcID?>" />
	
	<div class="clearfix">
		<label for="uEmail"><?=t('Email Address')?></label>
		<div class="input">
			<input type="text" name="uEmail" value="" class="ccm-input-text" >
		</div>
	</div>
	
	<div class="actions">
		<?=$form->submit('submit', t('Reset and Email Password') . ' &gt;')?>
	</div>
	
</form>


<? if (ENABLE_REGISTRATION == 1) { ?>
<div class="clearfix">
<h3><?=t('Not a Member')?></h3>
<p><?=t('Create a user account for use on this website.')?></p>
<p>
<a class="btn" href="<?=$this->url('/register')?>"><?=t('Register here!')?></a>
</p>
</div>
<? } ?>

<? } ?>