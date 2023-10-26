<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_SOCIAL_SHARE extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_social_share';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Custom Social Share', 'dicm-divi-custom-modules' );

		// Module Icon
		// This character will be rendered using etbuilder font-icon. For fully customized icon, create svg icon and
		// define its path on $this->icon_path property (see CustomCTAFull class)
		$this->icon             = 'j';

		// Toggle settings
		$this->settings_modal_toggles  = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dicm-divi-custom-modules' ),
					'button'       => esc_html__( 'Button', 'dicm-divi-custom-modules' ),
				),
			),
		);
	}

	/**
	 * Module's specific fields
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_fields() {
		return array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'dicm-divi-custom-modules' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Text entered here will appear as title.', 'dicm-divi-custom-modules' ),
				'toggle_slug'     => 'main_content',
			),
		);
	}

	/**
	 * Render module output
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attrs       List of unprocessed attributes
	 * @param string $content     Content being processed
	 * @param string $render_slug Slug of module that is used for rendering output
	 *
	 * @return string module's rendered output
	 */
	function render( $attrs, $content = null, $render_slug ) {
		// Module specific props added on $this->get_fields()
		$title                 = $this->props['title'];

		// Render button
		$button = $this->render_button( array(
			'button_text'      => $button_text,
		) );

		// Render module content
		$output = sprintf(
			'<h2 class="share-section-title tw-text-sm">%1$s</h2>',
			esc_html( $title )
		);

		$output .= '<div class="social-share-section tw-mt-2">';
		$output .= '<button class="share icon icon-share-linkedin" data-sharer="linkedin" data-url="http://www.linkedin.com/shareArticle?mini=true&url='.get_the_permalink().'" title="Share this - LinkedIn" aria-label="Share this - LinkedIn">';
		$output .= '<svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path fill="#606060" fill-rule="evenodd" transform="translate(-9 -41)" d="m9.33333333 45.6666667h2.66666667c.184 0 .3333333.1493333.3333333.3333333v8.6666667c0 .184-.1493333.3333333-.3333333.3333333h-2.66666667c-.184 0-.33333333-.1493333-.33333333-.3333333v-8.6666667c0-.184.14933333-.3333333.33333333-.3333333zm1.31999997-4.6666667c.9133334 0 1.6533334.746 1.6533334 1.6666667 0 .9206666-.74 1.6666666-1.6533334 1.6666666-.9133333 0-1.6533333-.746-1.6533333-1.6666666 0-.9206667.74-1.6666667 1.6533333-1.6666667zm9.3466667 14h2c.184 0 .3333333-.1493333.3333333-.3333333v-5.6c0-2.5133334-1.42-3.7333334-3.4066666-3.7333334-.7926667-.0286666-1.56.2786667-2.1133334.8466667-.0986666.114-.2713333.1266667-.3853333.028-.0606667-.0526667-.0946667-.128-.0946667-.208 0-.184-.1493333-.3333333-.3333333-.3333333h-2c-.184 0-.3333333.1493333-.3333333.3333333v8.6666667c0 .184.1493333.3333333.3333333.3333333h2c.184 0 .3333333-.1493333.3333333-.3333333v-5c0-.9206667.746-1.6666667 1.6666667-1.6666667s1.6666667.746 1.6666667 1.6666667v5c0 .184.1493333.3333333.3333333.3333333z"></path></svg>';
		$output .= '</button>';
		
		$output .= '<button class="share icon icon-share-twitter" data-sharer="twitter" data-url="https://twitter.com/home?status='.get_the_permalink().'" data-title="Test Case FAQs: How Do Test Cases Work?" data-via="applause" title="Share this - Twitter" aria-label="Share this - Twitter">';
		$output .= '<svg viewBox="0 0 14 12" xmlns="http://www.w3.org/2000/svg"><path fill="#606060" fill-rule="evenodd" d="m70.8908778 44.3504915c.1284289-.107226.145997-.298052.0393767-.4264809-.0411941-.0496754-.0975332-.0848116-.1605361-.1005623l-.4785795-.1211594c-.1623535-.0405883-.2610984-.2053651-.22051-.3671128.0048464-.0199913.012116-.038771.0205971-.0569449l.2665506-.5391591c.0726956-.1508434.0090869-.3319767-.1411507-.4046723-.065426-.0315014-.139939-.0381652-.2102114-.0193855l-1.2115936.3392462c-.0914753.0266551-.1902202.0084812-.2665506-.0484637-.5240142-.3931621-1.1619182-.6057968-1.8173903-.6057968-1.6726049 0-3.0289839 1.356379-3.0289839 3.0289839v.2180868c.0006058.0769362-.0569449.1423623-.1332752.1514492-1.702289.1999129-3.3318823-.6663764-5.088693-2.6897377-.0775419-.0860231-.1968839-.1211593-.3089563-.0908695-.1005623.0460406-.1678057.1435738-.1756811.2544347-.2429245.9965356-.1447854 2.0457757.2786665 2.9805201.0375594.074513.0078754.1659883-.0666376.2035477-.0284724.0139333-.0599739.0193855-.0908695.0145391l-.6784924-.1332753c-.1653825-.026655-.3204665.0860232-.3471216.2507999-.0054521.035742-.0048463.071484.0018174.1066202.1060145.942014.6342693 1.7840715 1.4357384 2.2899118.0751188.0363478.1066202.1272174.0702724.2023362-.0151449.0308956-.0393768.0557333-.0702724.0702724l-.3210723.1272173c-.155084.0623971-.2308086.2386839-.1684115.3937679.003029.0084812.0066638.0163565.0109043.0242319.3604491.7887474 1.0849821 1.350321 1.9385497 1.502376.0787536.0284724.119342.1157072.0908695.1938549-.0151449.0424058-.0484637.0751188-.0908695.0908696-.8238836.3404577-1.7077411.5118982-2.5988681.5028113-.1672-.0333188-.3301593.0751188-.3634781.2423187s.0751188.3301592.2423187.3634781c1.5435702.7318025 3.2234446 1.1304167 4.9311857 1.1691877 1.5017702.0230203 2.9774912-.3986142 4.2405775-1.2115935 2.1105959-1.4090833 3.3748938-3.7807777 3.36823-6.3184604v-.5270432c.0006058-.0890521.0405884-.1732578.1090434-.2302027z" transform="translate(-57 -42)"></path></svg>';
		$output .= '</button>';
		
		$output .= '<button class="share icon icon-share-facebook" data-sharer="facebook" data-url="https://www.facebook.com/sharer/sharer.php?u='.get_the_permalink().'" title="Share this - Facebook" aria-label="Share this - Facebook">';
		$output .= '<svg viewBox="0 0 8 14" xmlns="http://www.w3.org/2000/svg"><path fill="#606060" fill-rule="evenodd" d="m115.665 45.1825c-.054833-.0624167-.133-.098-.215833-.0991667h-2.199167v-.8225c0-.1633333.035-.35.2975-.35h1.75c.077583.0035.152833-.0285833.204167-.0875.051333-.0530833.0805-.12425.081666-.1983333v-2.3333333c0-.161-.130666-.2916667-.291666-.2916667h-2.525834c-2.8 0-3.015833 2.3916667-3.015833 3.1208333v.9625h-1.458333c-.161 0-.291667.1306667-.291667.2916667v2.3333333c0 .161.130667.2916667.291667.2916667h1.458333v6.7083333c0 .161.130667.2916667.291667.2916667h2.916666c.161 0 .291667-.1306667.291667-.2916667v-6.7083333h1.954167c.1505.0005833.2765-.1131667.291666-.2625l.245-2.3333333c.007-.0810834-.020416-.1615834-.075833-.2216667z" transform="translate(-108 -41)"></path></svg>';
		$output .= '</button>';
		
		$output .= '<button class="share icon icon-share-reddit" data-sharer="reddit" data-url="http://www.reddit.com/submit?url='.get_the_permalink().'" title="Share this - Reddit" aria-label="Share this - Reddit">';
		$output .= '<svg viewBox="0 0 16 14" xmlns="http://www.w3.org/2000/svg"><path fill="#606060" fill-rule="evenodd" d="m167.999724 47.2264702c-.003751-.8633838-.561113-1.6269044-1.382295-1.8935613-.821182-.2666568-1.72078.0237548-2.23104.7202324-1.177601-.7013466-2.510672-1.0998934-3.879985-1.1599957.04-.706664.246665-1.9333261.919996-2.3199913.419999-.2399991 1.03333-.1266662 1.80666.3066655.082542.810459.741 1.4412515 1.554254 1.4889517.813254.0477001 1.540939-.5017904 1.717662-1.2970426.176722-.7952522-.24975-1.6012221-1.006664-1.9024483-.756915-.3012261-1.620559-.0086785-2.038586.690543-.760862-.4865103-1.719337-.5445233-2.533324-.1533328-1.179995.6666642-1.393328 2.4333243-1.426661 3.1866548-1.371547.059286-2.706971.4578517-3.886652 1.1599957-.51026-.6964776-1.409858-.9868892-2.23104-.7202324-.821182.2666569-1.378544 1.0301775-1.382295 1.8935613-.003264.8503928.531577 1.6098663 1.333343 1.8933263-.00969.1465061-.00969.2934923 0 .4399984 0 2.5733237 2.993322 4.6666492 6.666642 4.6666492s6.666642-2.0933255 6.666642-4.6666492c.00969-.1465061.00969-.2934923 0-.4399984.801766-.28346 1.336606-1.0429335 1.333343-1.8933263zm-11.666638 1.3333321c0-.5522864.447714-1 1-1 .552279 0 1 .4477136 1 1 0 .552279-.447721 1-1 1-.552286 0-1-.447721-1-1zm6.27331 3.3333172c-.797633.4413396-1.695076.6708647-2.606657.6666642-.916313.008304-1.819087-.2214095-2.61999-.6666642-.239323-.1380707-.321404-.4440083-.183333-.6833308s.444008-.3214033.683331-.1833326c1.328133.6745915 2.898518.6745915 4.226651 0 .239322-.1380707.54526-.0559899.683331.1833326.13807.2393225.055989.5452601-.183333.6833308zm.060003-2.3333172c-.552286 0-1-.447721-1-1 0-.5522864.447714-1 1-1 .552279 0 1 .4477136 1 1 0 .552279-.447721 1-1 1z" transform="translate(-152 -41)"></path></svg>';
		$output .= '</button>';
		
		$output .= '<button class="share icon icon-share-email" data-sharer="email" data-url="mailto:?subject=&amp;body='.get_the_permalink().'" data-title="Test Case FAQs: How Do Test Cases Work?" data-subject="Test Case FAQs: How Do Test Cases Work?" title="Share this - Email" aria-label="Share this - Email">';
		$output .= '<svg viewBox="0 0 14 12" xmlns="http://www.w3.org/2000/svg"><g fill="#606060"><path d="m13.8939394 9.95041322c0 .95851068-.7122743 1.73553718-1.5909091 1.73553718h-10.6060606c-.87863483 0-1.5909139-.7770265-1.5909139-1.73553718v-5.48314049c-.00057614-.1052933.05154469-.20254526.13593971-.2536402s.18761937-.04789298.26921661.00835094l5.22348485 3.56132232c.77462045.52458867 1.754925.52458867 2.52954545 0l5.22348488-3.56132232c.0816831-.05624096.1849953-.05934698.2693939-.00809917.0843336.05116799.1368182.14811646.1368182.25338843z"></path><path d="m13.8568182 1.92413223-6.43257578 4.38743802c-.25823059.17477191-.58495122.17477191-.84318181 0l-6.43363637-4.38743802c-.02490658-.01429119-.04074371-.04219999-.04136363-.07289256 0-.95851072.71227426-1.73553719 1.59090909-1.73553719h10.6060606c.8786348 0 1.5909198.77702647 1.5909198 1.73553719.0004133.0286519-.0118218.05577734-.0328895.07289256z"></path></g></svg>';
		$output .= '</button>';
		
		$output .= '<button class="share icon icon-share-link tw-relative tw-whitespace-normal" data-href="'.get_the_permalink().'" title="Share this - Copy Link" aria-label="Share this - Copy Link">';
		$output .= '<span class="icon">';
		$output .= '<svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><g fill="#606060"><path d="m2.9288 13.2678-2.2092-2.2078c-.95757979-.9614431-.95757979-2.51615687 0-3.4776l1.6884-1.68c.25690426-.29726589.65827341-.42623343 1.04025018-.33425215s.68064194.38951941.7740667.7711457-.03402439.78348015-.33031688 1.04150645l-1.68 1.687c-.13992782.13995771-.13992782.36684229 0 .5068l2.2008 2.2078c.05580633.0856905.1511395.1373721.2534.1373721s.19759367-.0516816.2534-.1373721l1.6884-1.68c.41557609-.36190441 1.04022268-.34096057 1.43062402.0479676.39040134.3889281.41370664 1.013491.05337598 1.4304324l-1.68 1.687c-.46111714.4633754-1.08788312.7238876-1.7416.7238876s-1.28048286-.2605122-1.7416-.7238876z"></path><path d="m10.108 8.0808c-.19705876-.19693251-.30777584-.46410609-.30777584-.7427s.11071708-.54576749.30777584-.7427l1.687-1.687c.1400885-.14043781.1400885-.36776219 0-.5082l-2.2078-2.2078c-.05625093-.08505807-.1514243-.13622053-.2534-.13622053s-.19714907.05116246-.2534.13622053l-1.6884 1.6884c-.41218473.39773098-1.06707681.39173345-1.47190814-.0134798-.40483134-.40521325-.41021133-1.06011069-.01209186-1.4719202l1.687-1.687c.97328621-.92711113 2.5029138-.92711113 3.4762 0l2.2092 2.2078c.9574182.96096359.9574182 2.51523641 0 3.4762l-1.6884 1.6884c-.1966934.19697374-.4636352.30765603-.742.30765603s-.5453066-.11068229-.742-.30765603z"></path><path d="m5.208 8.7808c-.19705876-.19693251-.30777584-.46410609-.30777584-.7427s.11071708-.54576749.30777584-.7427l2.1-2.1c.41218473-.39773098 1.06707681-.39173345 1.47190814.0134798.40483134.40521325.41021133 1.06011069.01209186 1.4719202l-2.1 2.1c-.1966934.19697374-.46363522.30765603-.742.30765603s-.5453066-.11068229-.742-.30765603z"></path></g></svg>';
		$output .= '</span>';
		$output .= '<span class="status">';              
		$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="#606060"><path d="M2.99 9.375a1.713 1.713 0 0 1-1.321-.619L.161 6.839a.75.75 0 1 1 1.178-.928l1.5 1.9a.16.16 0 0 0 .158.063.217.217 0 0 0 .17-.082L8.664.907a.75.75 0 1 1 1.172.936L4.329 8.735A1.717 1.717 0 0 1 3 9.375Z" stroke-linecap="round" stroke-linejoin="round" stroke-width="0"></path></svg>';
		$output .= '</span>';
		$output .= '</button>';
		$output .= '</div>';

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_SOCIAL_SHARE;