<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Termmeta;
class TenantTermmetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $metas=array(
        array(
            "id" => 1,
            "term_id" => 1,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor"
        ),
        array(
            "id" => 2,
            "term_id" => 2,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi"
        ),
        array(
            "id" => 3,
            "term_id" => 3,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor"
        ),
        array(
            "id" => 4,
            "term_id" => 4,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi"
        ),
        array(
            "id" => 5,
            "term_id" => 5,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 6,
            "term_id" => 6,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 7,
            "term_id" => 7,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 8,
            "term_id" => 8,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 9,
            "term_id" => 9,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 10,
            "term_id" => 10,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 11,
            "term_id" => 11,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 12,
            "term_id" => 12,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 13,
            "term_id" => 13,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 14,
            "term_id" => 14,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 15,
            "term_id" => 15,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 16,
            "term_id" => 16,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 17,
            "term_id" => 17,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 18,
            "term_id" => 18,
            "key" => "excerpt",
            "value" => "product short description"
        ),
        array(
            "id" => 19,
            "term_id" => 1,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae4a4957dc0901221641735332.webp"
        ),
        array(
            "id" => 20,
            "term_id" => 1,
            "key" => "gallery",
            "value" => '["uploads/dummy/21/11/61a5d7f0f02803011211638258672.jpeg","uploads/dummy/21/11/61a5d6aaa53c03011211638258346.jpeg"]'
        ),
        array(
            "id" => 31,
            "term_id" => 20,
            "key" => "excerpt",
            "value" => "There are many variations of passages of Lorem Ipsumavailable."
        ),
        array(
            "id" => 32,
            "term_id" => 20,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/21/12/61ae4a937336a0612211638812307.jpg"
        ),
        array(
            "id" => 33,
            "term_id" => 20,
            "key" => "description",
            "value" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
        ),
        array(
            "id" => 34,
            "term_id" => 21,
            "key" => "excerpt",
            "value" => "There are many variations of passages of Lorem Ipsumavailable.There are many variations of passages of Lorem Ipsumavailable.There are many variations of passages of Lorem Ipsumavailable."
        ),
        array(
            "id" => 35,
            "term_id" => 21,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/21/12/61ae4a93836d50612211638812307.jpg"
        ),
        array(
            "id" => 36,
            "term_id" => 21,
            "key" => "description",
            "value" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
        ),
        array(
            "id" => 37,
            "term_id" => 22,
            "key" => "excerpt",
            "value" => "There are many variations of passages of Lorem Ipsum available."
        ),
        array(
            "id" => 38,
            "term_id" => 22,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/21/12/61ae4a939290c0612211638812307.jpg"
        ),
        array(
            "id" => 39,
            "term_id" => 22,
            "key" => "description",
            "value" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
        ),
        array(
            "id" => 40,
            "term_id" => 23,
            "key" => "excerpt",
            "value" => "There are many variations of passages of Lorem Ipsum available."
        ),
        array(
            "id" => 41,
            "term_id" => 23,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/21/12/61ae4a937336a0612211638812307.jpg"
        ),
        array(
            "id" => 42,
            "term_id" => 23,
            "key" => "description",
            "value" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
        ),
        array(
            "id" => 43,
            "term_id" => 24,
            "key" => "excerpt",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 44,
            "term_id" => 1,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 46,
            "term_id" => 1,
            "key" => "seo",
            "value" => '{"preview":"/uploads/dummy/21/12/61af7a4466fcf0712211638890052.jpg","title":"Lorem ipsum indoor plants","tags":"food, fair","description":"testing"}'
        ),
        array(
            "id" => 47,
            "term_id" => 24,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae5acd6f8f0901221641735596.webp"
        ),
        array(
            "id" => 48,
            "term_id" => 24,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 49,
            "term_id" => 2,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 50,
            "term_id" => 2,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae5839945c0901221641735555.webp"
        ),
        array(
            "id" => 51,
            "term_id" => 3,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae581602880901221641735553.webp"
        ),
        array(
            "id" => 52,
            "term_id" => 4,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae57eeb1fe0901221641735550.webp"
        ),
        array(
            "id" => 53,
            "term_id" => 5,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/21/12/61b8bbf0aded91412211639496688.jpeg"
        ),
        array(
            "id" => 54,
            "term_id" => 6,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae4a4957dc0901221641735332.webp"
        ),
        array(
            "id" => 55,
            "term_id" => 7,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae57eeb1fe0901221641735550.webp"
        ),
        array(
            "id" => 56,
            "term_id" => 8,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae5acd6f8f0901221641735596.webp"
        ),
        array(
            "id" => 57,
            "term_id" => 9,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae5839945c0901221641735555.webp"
        ),
        array(
            "id" => 58,
            "term_id" => 10,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/22/01/61dae4a4957dc0901221641735332.webp"
        ),
        array(
            "id" => 59,
            "term_id" => 11,
            "key" => "preview",
            "value" => env('APP_URL')."/uploads/dummy/21/12/61b8bbf0aded91412211639496688.jpeg"
        ),
        array(
            "id" => 60,
            "term_id" => 3,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 61,
            "term_id" => 4,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 62,
            "term_id" => 5,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 63,
            "term_id" => 7,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 64,
            "term_id" => 8,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 65,
            "term_id" => 9,
            "key" => "description",
            "value" => "Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem Quo vel minus ea dolor est quis ut perferendis illo voluptas temporibus aut nisi error ad tempor excepturi voluptatem"
        ),
        array(
            "id" => 45,
            "term_id" => 26,
            "key" => "meta",
            "value" => '{"page_excerpt":null,"page_content":"<p>Last updated&nbsp;<strong>[Date]<\/strong><\/p><p>In addition, you agree to Booostr &nbsp;<a href=\"https:\/\/terms.pscr.pt\/legal\/shop\/spraytesting\/terms_of_service\" target=\"_blank\">Messaging Terms<\/a>&nbsp;and&nbsp;<a href=\"https:\/\/terms.pscr.pt\/legal\/shop\/spraytesting\/privacy_policy\" target=\"_blank\">Messaging Privacy Policy<\/a>.<\/p><p>Thank you for choosing to be part of our community at&nbsp;<strong>[club_name]<\/strong>&nbsp;(\u201cCompany\u201d, \u201cwe\u201d, \u201cus\u201d, or \u201cour\u201d). We are committed to protecting your personal information and your right to privacy. If you have any questions or concerns about this privacy notice, or our practices with regards to your personal information, please contact us at&nbsp;<strong>[club_email].<\/strong><\/p><p>When you visit our club profile&nbsp;<strong>[club_profile_url]<\/strong>&nbsp;(the \"Website\"), and more generally, use any of our services (the \"Services\", which include the Website), we appreciate that you are trusting us with your personal information. We take your privacy very seriously. In this privacy notice, we seek to explain to you in the clearest way possible what information we collect, how we use it and what rights you have in relation to it. We hope you take some time to read through it carefully, as it is important. If there are any terms in this privacy notice that you do not agree with, please discontinue use of our Services immediately.<\/p><p>This privacy notice applies to all information collected through our Services (which, as described above, includes our Website), as well as any related services, sales, marketing or events.<\/p><p>Please read this privacy notice carefully as it will help you understand what we do with the information that we collect.<\/p><p>&nbsp;<\/p><p><strong>TABLE OF CONTENTS<\/strong><\/p><ol style=\"list-style: decimal\"><li>WHAT INFORMATION DO WE COLLECT?<\/li><li>HOW DO WE USE YOUR INFORMATION?<\/li><li>WILL YOUR INFORMATION BE SHARED WITH ANYONE?<\/li><li>DO WE USE COOKIES AND OTHER TRACKING TECHNOLOGIES?<\/li><li>DO WE USE GOOGLE MAPS?<\/li><li>HOW LONG DO WE KEEP YOUR INFORMATION?<\/li><li>HOW DO WE KEEP YOUR INFORMATION SAFE?<\/li><li>WHAT ARE YOUR PRIVACY RIGHTS?<\/li><li>CONTROLS FOR DO-NOT-TRACK FEATURES<\/li><li>DO CALIFORNIA RESIDENTS HAVE SPECIFIC PRIVACY RIGHTS?<\/li><li>DO WE MAKE UPDATES TO THIS NOTICE?<\/li><li>HOW CAN YOU CONTACT US ABOUT THIS NOTICE?<\/li><\/ol><p>&nbsp;<\/p><ol  ><li><strong>WHAT INFORMATION DO WE COLLECT?<\/strong><\/li><\/ol><p>&nbsp;<\/p><p>Personal information you disclose to us.<\/p><p><em>In Short:&nbsp; We collect information that you provide to us.<\/em><\/p><p>We collect personal information that you voluntarily provide to us when you register on the Website, express an interest in obtaining information about us or our products and Services, when you participate in activities on the Website or otherwise when you contact us.<\/p><p>The personal information that we collect depends on the context of your interactions with us and the Website, the choices you make and the products and features you use. The personal information we collect may include the following:<\/p><p>Personal Information Provided by You. We collect names; phone numbers; email addresses; mailing addresses; usernames; passwords; billing addresses; debit\/credit card numbers; and other similar information.<\/p><p>Payment Data. We may collect data necessary to process your payment if you make purchases, such as your payment instrument number (such as a credit card number), and the security code associated with your payment instrument. All payment data is stored by Stripe. You may find their privacy notice link(s) here:&nbsp;https:\/\/stripe.com\/privacy.<\/p><p>All personal information that you provide to us must be true, complete and accurate, and you must notify us of any changes to such personal information.<\/p><p>&nbsp;<\/p><p><strong>Information automatically collected<\/strong><\/p><p><em>In Short:&nbsp; Some information \u2014 such as your Internet Protocol (IP) address and\/or browser and device characteristics \u2014 is collected automatically when you visit our&nbsp;Website.<\/em><\/p><p>We automatically collect certain information when you visit, use or navigate the&nbsp;Website. This information does not reveal your specific identity (like your name or contact information) but may include device and usage information, such as your IP address, browser and device characteristics, operating system, language preferences, referring URLs, device name, country, location, information about who and when you use our&nbsp;Website&nbsp;and other technical information. This information is primarily needed to maintain the security and operation of our&nbsp;Website, and for our internal analytics and reporting purposes.<\/p><p>Like many businesses, we also collect information through cookies and similar technologies.&nbsp;&nbsp;<\/p><p><strong>The information we collect includes:<\/strong><\/p><ul  ><li><em>Log and Usage Data.<\/em>&nbsp;Log and usage data is service-related, diagnostic usage and performance information our servers automatically collect when you access or use our Website and which we record in log files. Depending on how you interact with us, this log data may include your IP address, device information, browser type and settings and information about your activity in the Website (such as the date\/time stamps associated with your usage, pages and files viewed, searches and other actions you take such as which features you use), device event information (such as system activity, error reports (sometimes called \'crash dumps\') and hardware settings).<\/li><\/ul><ul  ><li><em>Device Data.&nbsp;<\/em>We collect device data such as information about your computer, phone, tablet or other device you use to access the&nbsp;Website. Depending on the device used, this device data may include information such as your IP address (or proxy server), device application identification numbers, location, browser type, hardware model Internet service provider and\/or mobile carrier, operating system configuration information.<\/li><\/ul><ul  ><li><em>Location Data.<\/em>&nbsp;We collect information data such as information about your device\'s location, which can be either precise or imprecise. How much information we collect depends on the type of settings of the device you use to access the Website. For example, we may use GPS and other technologies to collect geolocation data that tells us your current location (based on your IP address). You can opt out of allowing us to collect this information either by refusing access to the information or by disabling your Locations settings on your device. Note however, if you choose to opt out, you may not be able to use certain aspects of the Services.<\/li><\/ul><p>&nbsp;<\/p><ol start=\"2\"  ><li><strong>HOW DO WE USE YOUR INFORMATION?<\/strong><\/li><\/ol><p><em>In Short:&nbsp; We process your information for purposes based on legitimate business interests, the fulfillment of our contract with you, compliance with our legal obligations, and\/or your consent.<\/em><\/p><p>We use personal information collected via our Website for a variety of business purposes described below. We process your personal information for these purposes in reliance on our legitimate business interests, in order to enter into or perform a contract with you, with your consent, and\/or for compliance with our legal obligations. We indicate the specific processing grounds we rely on next to each purpose listed below.<\/p><p><strong>We use the information we collect or receive:<\/strong><\/p><ul  ><li>To facilitate account creation and logon process. If you choose to link your account with us to a third-party account (such as your Google or Facebook account), we use the information you allowed us to collect from those third parties to facilitate account creation and logon process for the performance of the contract.<br><br><\/li><li>To post testimonials. We post testimonials on our Website that may contain personal information. Prior to posting a testimonial, we will obtain your consent to use your name and the consent of the testimonial.If you wish to update, or delete your testimonial, please contact us at&nbsp;<strong>[club_email]&nbsp;<\/strong>and be sure to include your name, testimonial location, and contact information.<br><br><\/li><li>Request feedback. We may use your information to request feedback and to contact you about your use of our Website.<br><br><\/li><li>To enable user-to-user communications. We may use your information in order to enable user-to-user communications with each user\'s consent.<br><br><\/li><li>To manage user accounts. We may use your information for the purposes of managing our account and keeping it in working order.<\/li><\/ul><ul  ><li>To send administrative information to you. We may use your personal information to send you product, service and new feature information and\/or information about changes to our terms, conditions, and policies.<br><br><\/li><li>To protect our Services. We may use your information as part of our efforts to keep our Website safe and secure (for example, for fraud monitoring and prevention).<br><br><\/li><li>To enforce our terms, conditions and policies for business purposes, to comply with legal and regulatory requirements or in connection with our contract.<br><br><\/li><li>To respond to legal requests and prevent harm. If we receive a subpoena or other legal request, we may need to inspect the data we hold to determine how to respond.<\/li><\/ul><ul  ><li>Fulfill and manage your orders. We may use your information to fulfill and manage your orders, payments, returns, and exchanges made through the Website.<br><br><\/li><li>Administer prize draws and competitions. We may use your information to administer prize draws and competitions when you elect to participate in our competitions.<br><br><\/li><li>To deliver and facilitate delivery of services to the user. We may use your information to provide you with the requested service.<br><br><\/li><li>To respond to user inquiries\/offer support to users. We may use your information to respond to your inquiries and solve any potential issues you might have with the use of our Services.<\/li><\/ul><ul  ><li>To send you marketing and promotional communications. We and\/or our third-party marketing partners may use the personal information you send to us for our marketing purposes, if this is in accordance with your marketing preferences. For example, when expressing an interest in obtaining information about us or our Website, subscribing to marketing or otherwise contacting us, we will collect personal information from you. You can opt-out of our marketing emails at any time (see the \"<a href=\"https:\/\/app.termly.io\/dashboard\/website\/552127\/privacy-policy#privacyrights\" target=\"_blank\">WHAT ARE YOUR PRIVACY RIGHTS<\/a>\" below).<br><br><\/li><li>Deliver targeted advertising to you. We may use your information to develop and display personalized content and advertising (and work with third parties who do so) tailored to your interests and\/or location and to measure its effectiveness.<\/li><\/ul><ul  ><li>For other business purposes. We may use your information for other business purposes, such as data analysis, identifying usage trends, determining the effectiveness of our promotional campaigns and to evaluate and improve our Website, products, marketing and your experience. We may use and store this information in aggregated and anonymized form so that it is not associated with individual end users and does not include personal information. We will not use identifiable personal information without your consent.<\/li><\/ul><p>&nbsp;<\/p><ol start=\"3\"  ><li><strong>WILL YOUR INFORMATION BE SHARED WITH ANYONE?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>We only share information with your consent, to comply with laws, to provide you with services, to protect your rights, or to fulfill business obligations.<\/em><\/p><p>We may process or share your data that we hold based on the following legal basis:<\/p><ul  ><li>Consent: We may process your data if you have given us specific consent to use your personal information in a specific purpose.<br><br><\/li><li>Legitimate Interests: We may process your data when it is reasonably necessary to achieve our legitimate business interests.<br><br><\/li><li>Performance of a Contract: Where we have entered into a contract with you, we may process your personal information to fulfill the terms of our contract.<br><br><\/li><li>Legal Obligations: We may disclose your information where we are legally required to do so in order to comply with applicable law, governmental requests, a judicial proceeding, court order, or legal process, such as in response to a court order or a subpoena (including in response to public authorities to meet national security or law enforcement requirements).<br><br><\/li><li>Vital Interests: We may disclose your information where we believe it is necessary to investigate, prevent, or take action regarding potential violations of our policies, suspected fraud, situations involving potential threats to the safety of any person and illegal activities, or as evidence in litigation in which we are involved.<\/li><\/ul><p>More specifically, we may need to process your data or share your personal information in the following situations:<\/p><ul  ><li>Business Transfers. We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.<\/li><\/ul><p>&nbsp;<\/p><ol start=\"4\"  ><li><strong>DO WE USE COOKIES AND OTHER TRACKING TECHNOLOGIES?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>We may use cookies and other tracking technologies to collect and store your information.<\/em><\/p><p>We may use cookies and similar tracking technologies (like web beacons and pixels) to access or store information. Specific information about how we use such technologies and how you can refuse certain cookies is set out in our Cookie Notice.<\/p><p>&nbsp;<\/p><ol start=\"5\"  ><li><strong>DO WE USE GOOGLE MAPS?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>Yes, we use Google Maps for the purpose of providing better service.<\/em><\/p><p>This Website uses Google Maps APIs which is subject to Google\'s Terms of Service. You may find the Google Maps APIs Terms of Service&nbsp;<a href=\"https:\/\/developers.google.com\/maps\/terms\" target=\"_blank\">here<\/a>.To find out more about Google\u2019s Privacy Policy, please refer to this&nbsp;<a href=\"https:\/\/policies.google.com\/privacy\" target=\"_blank\">link<\/a>.<\/p><p>&nbsp;<\/p><ol start=\"6\"  ><li><strong>HOW LONG DO WE KEEP YOUR INFORMATION?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>We keep your information for as long as necessary to fulfill the purposes outlined in this privacy notice unless otherwise required by law.<\/em><\/p><p>We will only keep your personal information for as long as it is necessary for the purposes set out in this privacy notice, unless a longer retention period is required or permitted by law (such as tax, accounting or other legal requirements). No purpose in this notice will require us keeping your personal information for longer than&nbsp;the period of time in which users have an account with us.<\/p><p>When we have no ongoing legitimate business need to process your personal information, we will either delete or anonymize such information, or, if this is not possible (for example, because your personal information has been stored in backup archives), then we will securely store your personal information and isolate it from any further processing until deletion is possible.<\/p><p>&nbsp;<\/p><ol start=\"7\"  ><li><strong>HOW DO WE KEEP YOUR INFORMATION SAFE?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>We aim to protect your personal information through a system of organizational and technical security measures.<\/em><\/p><p>We have implemented appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, despite our safeguards and efforts to secure your information, no electronic transmission over the Internet or information storage technology can be guaranteed to be 100% secure, so we cannot promise or guarantee that hackers, cybercriminals, or other unauthorized third parties will not be able to defeat our security, and improperly collect, access, steal, or modify your information. Although we will do our best to protect your personal information, transmission of personal information to and from our Website is at your own risk. You should only access the Website within a secure environment.<\/p><p>&nbsp;<\/p><ol start=\"8\"  ><li><strong>WHAT ARE YOUR PRIVACY RIGHTS?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>You may review, change, or terminate your account at any time.<\/em><\/p><p>If you are resident in the European Economic Area and you believe we are unlawfully processing your personal information, you also have the right to complain to your local data protection supervisory authority. You can find their contact details here:&nbsp;<a href=\"http:\/\/ec.europa.eu\/justice\/data-protection\/bodies\/authorities\/index_en.htm\" target=\"_blank\">http:\/\/ec.europa.eu\/justice\/data-protection\/bodies\/authorities\/index_en.htm<\/a>.<\/p><p>If you are resident in Switzerland, the contact details for the data protection authorities are available here:&nbsp;<a href=\"https:\/\/www.edoeb.admin.ch\/edoeb\/en\/home.html\" target=\"_blank\">https:\/\/www.edoeb.admin.ch\/edoeb\/en\/home.html<\/a>.<\/p><p>If you have questions or comments about your privacy rights, you may email us at&nbsp;<strong>[club_email]<\/strong>.<\/p><p>&nbsp;<\/p><p><strong>Account Information<\/strong><\/p><p>If you would at any time like to review or change the information in your account or terminate your account, you can:<\/p><p>&nbsp;&nbsp;&nbsp;&nbsp;\u25a0&nbsp;&nbsp;Log in to your account settings and update your user account.<\/p><p>&nbsp;&nbsp;&nbsp;&nbsp;\u25a0&nbsp;&nbsp;Contact us using the contact information provided.<\/p><p>Upon your request to terminate your account,we will deactivate or delete your account and information from our active databases. However, we may retain some information in our files to prevent fraud, troubleshoot problems, assist with any investigations, enforce our Terms of Use and\/or comply with applicable legal requirements.<\/p><p>Cookies and similar technologies:&nbsp;Most Web browsers are set to accept cookies by default. If you prefer, you can usually choose to set your browser to remove cookies and to reject cookies. If you choose to remove cookies or reject cookies, this could affect certain features or services of our Website. To opt-out of interest-based advertising by advertisers on our Website visit&nbsp;<a href=\"http:\/\/www.aboutads.info\/choices\/\" target=\"_blank\">http:\/\/www.aboutads.info\/choices\/<\/a>.<\/p><p>Opting out of email marketing:&nbsp;You can unsubscribe from our marketing email list at any time by clicking on the unsubscribe link in the emails that we send or by contacting us using the details provided below. You will then be removed from the marketing email list \u2013 however, we may still communicate with you, for example to send you service-related emails that are necessary for the administration and use of your account, to respond to service requests,or for other non-marketing purposes. To otherwise opt-out, you may:<\/p><p>&nbsp;&nbsp;&nbsp;&nbsp;\u25a0&nbsp;&nbsp;Access your account settings and update your preferences.<\/p><p>&nbsp;&nbsp;&nbsp;&nbsp;\u25a0&nbsp;&nbsp;Contact us using the contact information provided.<\/p><p>&nbsp;<\/p><ol ><li><strong> CONTROLS FOR DO-NOT-TRACK FEATURES<\/strong><\/li><\/ol><p>Most web browsers and some mobile operating systems and mobile applications include a Do-Not-Track (\u201cDNT\u201d) feature or setting you can activate to signal your privacy preference not to have data about your online browsing activities monitored and collected. At this stage, no uniform technology standard for recognizing and implementing DNT signals has been finalized.As such, we do not currently respond to DNT browser signals or any other mechanism that automatically communicates your choice not to be tracked online. If a standard for online tracking is adopted that we must follow in the future, we will inform you about that practice in a revised version of this privacy notice.<\/p><p>&nbsp;<\/p><ol start=\"10\"  ><li><strong>DO CALIFORNIA RESIDENTS HAVE SPECIFIC PRIVACY RIGHTS?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>Yes, if you are a resident of California, you are granted specific rights regarding access to your personal information.<\/em><\/p><p>California Civil Code Section 1798.83, also known as the \u201cShine The Light\u201d law, permits our users who are California residents to request and obtain from us, once a year and free of charge, information about categories of personal information (if any) we disclosed to third parties for direct marketing purposes and the names and addresses of all third parties with which we shared personal information in the immediately preceding calendar year. If you are a California resident and would like to make such a request, please submit your request in writing to us using the contact information provided below.<\/p><p> If you are under 18 years of age, reside in California, and have a registered account with the Website, you have the right to request removal of unwanted data that you publicly post on the Website. To request removal of such data, please contact us using the contact information provided below, and include the email address associated with your account and a statement that you reside in California. We will make sure the data is not publicly displayed on the Website, but please be aware that the data may not be completely or comprehensively removed from all our systems (e.g. backups, etc.).&nbsp;&nbsp;<\/p><p>&nbsp;<\/p><p><strong>CCPA Privacy Notice<\/strong><\/p><p>The California Code of Regulations defines a \"resident\" as:<\/p><p>(1) every individual who is in the State of California for other than a temporary or transitory purpose and<\/p><p>(2) every individual who is domiciled in the State of California who is outside the State of California for a temporary or transitory purpose<\/p><p>All other individuals are defined as \"non-residents.\"<\/p><p>If this definition of \"resident\" applies to you, certain rights and obligations apply regarding your personal information.<\/p><p>What categories of personal information do we collect?<\/p><p>We have collected the following categories of personal information in the past twelve (12) months:<\/p><p>&nbsp;<\/p><table class=\"mce-item-table\"><tbody><tr><td><p><strong>Category<\/strong><\/p><\/td><td><p><strong>Examples<\/strong><\/p><\/td><td><p><strong>Collected<\/strong><\/p><\/td><\/tr><tr><td><p>A. Identifiers<\/p><\/td><td><p>Contact details, such as real name, alias, postal address, telephone or mobile contact number, unique personal identifier, online identifier, Internet Protocol address, email address and account name<\/p><\/td><td><p><br><\/p><p>YES<\/p><\/td><\/tr><tr><td><p>B. Personal information categories listed in the California Customer Records statute<\/p><\/td><td><p>Name, contact information, education, employment, employment history and financial information<\/p><\/td><td><p><br><\/p><p>YES<\/p><\/td><\/tr><tr><td><p>C. Protected classification characteristics under California or federal law<\/p><\/td><td><p>Gender and date of birth<\/p><\/td><td><p><br><\/p><p>YES<\/p><\/td><\/tr><tr><td><p>D. Commercial information<\/p><\/td><td><p>Transaction information, purchase history, financial details and payment information<\/p><\/td><td><p><br><\/p><p>YES<\/p><\/td><\/tr><tr><td><p>E. Biometric information<\/p><\/td><td><p>Fingerprints and voiceprints<\/p><\/td><td><p><br><\/p><p>NO<\/p><\/td><\/tr><tr><td><p>F. Internet or other similar network activity<\/p><\/td><td><p>Browsing history, search history, online behavior, interest data, and interactions with our and other websites, applications, systems and advertisements<\/p><\/td><td><p><br><\/p><p>YES<\/p><\/td><\/tr><tr><td><p>G. Geolocation data<\/p><\/td><td><p>Device location<\/p><\/td><td><p><br><\/p><p>YES<\/p><\/td><\/tr><tr><td><p>H. Audio, electronic, visual, thermal, olfactory, or similar information<\/p><\/td><td><p>Images and audio, video or call recordings created in connection with our business activities<\/p><\/td><td><p><br><\/p><p>NO<\/p><\/td><\/tr><tr><td><p>I. Professional or employment-related information<\/p><\/td><td><p>Business contact details in order to provide you our services at a business level, job title as well as work history and professional qualifications if you apply for a job with us<\/p><\/td><td><p><br><\/p><p>NO<\/p><\/td><\/tr><tr><td><p>J. Education Information<\/p><\/td><td><p>Student records and directory information<\/p><\/td><td><p><br><\/p><p>NO<\/p><\/td><\/tr><tr><td><p>K. Inferences drawn from other personal information<\/p><\/td><td><p>Inferences drawn from any of the collected personal information listed above to create a profile or summary about, for example, an individual\u2019s preferences and characteristics<\/p><\/td><td><p><br><\/p><p>NO<\/p><\/td><\/tr><\/tbody><\/table><p>&nbsp;<\/p><p>We may also collect other personal information outside of these categories in instances where you interact with us in-person, online, or by phone or mail in the context of:<\/p><ul  ><li>Receiving help through our customer support channels<br><br><\/li><li>Participation in customer surveys or contests; and<br><br><\/li><li>Facilitation in the delivery of our Services and to respond to your inquiries<\/li><\/ul><p><strong>How do we use and share your personal information?<\/strong><\/p><p>More information about our data collection and sharing practices can be found in this privacy notice.<\/p><p>You may contact us by email at&nbsp;<strong>[club_email]<\/strong>, or by referring to the contact details at the bottom of this document.<\/p><p>If you are using an authorized agent to exercise your right to opt-out, we may deny a request if the authorized agent does not submit proof that they have been validly authorized to act on your behalf.<\/p><p>&nbsp;<\/p><p><strong>Will your information be shared with anyone else?<\/strong><\/p><p>We may disclose your personal information with our service providers pursuant to a written contract between us and each service provider. Each service provider is a for-profit entity that processes the information on our behalf.<\/p><p>We may use your personal information for our own business purposes, such as for undertaking internal research for technological development and demonstration.This is not considered to be \"selling\" of your personal data.<\/p><p>Spray Planet has not disclosed or sold any personal information to third parties for a business or commercial purpose in the preceding twelve (12) months. Spray Planet will not sell personal information in the future belonging to website visitors, users and other consumers.<\/p><p>&nbsp;<\/p><p><strong>Your rights with respect to your personal data<\/strong><\/p><p><strong>Right to request deletion of the data - Request to delete<\/strong><\/p><p>You can ask for the deletion of your personal information. If you ask us to delete your personal information, we will respect your request and delete your personal information, subject to certain exceptions provided by law, such as (but not limited to) the exercise by another consumer of his or her right to free speech, our compliance requirements resulting from a legal obligation or any processing that may be required to protect against illegal activities.<\/p><p>&nbsp;<\/p><p><strong>Right to be informed - Request to know<\/strong><\/p><p>Depending on the circumstances, you have a right to know:<\/p><ul  ><li>whether we collect and use your personal information;<\/li><li>the categories of personal information that we collect;<\/li><li>the purposes for which the collected personal information is used;<\/li><li>whether we sell your personal information to third parties;<\/li><li>the categories of personal information that we sold or disclosed for a business purpose;<\/li><li>the categories of third parties to whom the personal information was sold or disclosed for a business purpose; and<\/li><li>the business or commercial purpose for collecting or selling personal information.<\/li><\/ul><p>In accordance with applicable law, we are not obligated to provide or delete consumer information that is de-identified in response to a consumer request or to re-identify individual data to verify a consumer request.<\/p><p>&nbsp;<\/p><p><strong>Right to Non-Discrimination for the Exercise of a Consumer\'s Privacy Rights<\/strong><\/p><p>We will not discriminate against you if you exercise your privacy rights.<\/p><p><strong>Verification process<\/strong><\/p><p>Upon receiving your request, we will need to verify your identity to determine you are the same person about whom we have the information in our system. These verification efforts require us to ask you to provide information so that we can match it with the information you have previously provided us. For instance, depending on the type of request you submit, we may ask you to provide certain information so that we can match the information you provide with the information we already have on file, or we may contact you through a communication method (e.g. phone or email) that you have previously provided to us. We may also use other verification methods as the circumstances dictate.<\/p><p>We will only use personal information provided in your request to verify your identity or authority to make the request. To the extent possible, we will avoid requesting additional information from you for the purposes of verification. If, however, if we cannot verify your identity from the information already maintained by us, we may request that you provide additional information for the purposes of verifying your identity, and for security or fraud-prevention purposes. We will delete such additionally provided information as soon as we finish verifying you.<\/p><p><strong>Other privacy rights<\/strong><\/p><ul  ><li>you may object to the processing of your personal data<\/li><li>you may request correction of your personal data if it is incorrect or no longer relevant, or ask to restrict the processing of the data<\/li><li>you can designate an authorized agent to make a request under the CCPA on your behalf. We may deny a request from an authorized agent that does not submit proof that they have been validly authorized to act on your behalf in accordance with the CCPA.<\/li><li>you may request to opt-out from future selling of your personal information to third parties. Upon receiving a request to opt-out, we will act upon the request as soon as feasibly possible, but no later than 15 days from the date of the request submission.<\/li><\/ul><p>To exercise these rights, you can contact us by email at&nbsp;<strong>[club_email]<\/strong>, or by referring to the contact details at the bottom of this document. If you have a complaint about how we handle your data, we would like to hear from you.<\/p><p>&nbsp;<\/p><ol start=\"11\"  ><li><strong>DO WE MAKE UPDATES TO THIS NOTICE?<\/strong><\/li><\/ol><p><em>In Short:&nbsp;<\/em>&nbsp;<em>Yes, we will update this notice as necessary to stay compliant with relevant laws.<\/em><\/p><p>We may update this privacy notice from time to time. The updated version will be indicated by an updated \u201cRevised\u201d date and the updated version will be effective as soon as it is accessible. If we make material changes to this privacy notice, we may notify you either by prominently posting a notice of such changes or by directly sending you a notification. We encourage you to review this privacy notice frequently to be informed of how we are protecting your information.<\/p><p>&nbsp;<\/p><ol start=\"12\"  ><li><strong>HOW CAN YOU CONTACT US ABOUT THIS NOTICE?<\/strong><\/li><\/ol><p>If you have questions or comments about this notice, you may contact our Data Protection Officer (DPO),&nbsp;<strong>[club_manager_first_and_last_name]<\/strong>, by email at&nbsp;<strong>[club_email]<\/strong>,&nbsp;or by post to:<\/p><p><strong>[club_name]<\/strong><br><strong>[club_address]<\/strong><\/p><p>&nbsp;<\/p><p><strong>HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?<\/strong><\/p><p>Based on the applicable laws of your country, you may have the right to request access to the personal information we collect from you, change that information, or delete it in some circumstances. To request to review, update, or delete your personal information, please visit:&nbsp;<strong>[club_email]<\/strong>,. We will respond to your request within 30 days.<\/p><p>&nbsp;<\/p><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4060px; height: 4px; width: 844px; \" data-row=\"0\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4060px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4127px; height: 4px; width: 844px; \" data-row=\"1\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4127px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4192px; height: 4px; width: 844px; \" data-row=\"2\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4192px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4257px; height: 4px; width: 844px; \" data-row=\"3\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4257px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4322px; height: 4px; width: 844px; \" data-row=\"4\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4322px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4387px; height: 4px; width: 844px; \" data-row=\"5\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4387px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4452px; height: 4px; width: 844px; \" data-row=\"6\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4452px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4517px; height: 4px; width: 844px; \" data-row=\"7\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4517px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4582px; height: 4px; width: 844px; \" data-row=\"8\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4582px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4647px; height: 4px; width: 844px; \" data-row=\"9\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4647px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4712px; height: 4px; width: 844px; \" data-row=\"10\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4712px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-row\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: row-resize; margin: 0; padding: 0; position: absolute; left: 8px; top: 4775px; height: 4px; width: 844px; \" data-row=\"11\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: row-resize; margin: 0px; padding: 0px; position: absolute; left: 8px; top: 4775px; height: 4px; width: 844px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-col\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: col-resize; margin: 0; padding: 0; position: absolute; left: 259.15625px; top: 4018px; height: 762px; width: 4px; \" data-col=\"0\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: col-resize; margin: 0px; padding: 0px; position: absolute; left: 259.156px; top: 4018px; height: 762px; width: 4px;\"><\/div><div data-mce-bogus=\"all\" class=\"mce-resize-bar mce-resize-bar-col\" unselectable=\"on\" data-mce-resize=\"false\" data-mce-style=\"cursor: col-resize; margin: 0; padding: 0; position: absolute; left: 786.15625px; top: 4018px; height: 762px; width: 4px; \" data-col=\"1\" style=\"color: rgb(0, 0, 0); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: col-resize; margin: 0px; padding: 0px; position: absolute; left: 786.156px; top: 4018px; height: 762px; width: 4px;\"><\/div>"}'
        ),
        array(
            "id" => 66,
            "term_id" => 25,
            "key" => "meta",
            "value" => '{"page_excerpt":null,"page_content":"Last updated: [Date]<br><br>Please read these Terms and Conditions (\"Terms,\" \"Terms and Conditions\") carefully before using the [store_url] website (the \"Website\") operated by [store_name] (\"us,\" \"we,\" or \"our\").<br><br>Your access to and use of the Website is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users, and others who access or use the Website. By accessing or using the Website, you agree to be bound by these Terms. If you disagree with any part of these Terms, please do not use the Website.<br><br>Use of the Website\r\n<br>Content: The content on the Website is for general informational purposes only. We reserve the right to modify or discontinue any aspect of the Website at any time.<br><br>User Accounts: If you create an account on the Website, you are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer. You agree to accept responsibility for all activities that occur under your account.<br><br>User Content: You may submit content to the Website, including but not limited to comments and feedback. By submitting such content, you grant us the right to use, reproduce, modify, and distribute it.<br><br>Intellectual Property<br>The Website and its original content, features, and functionality are owned by [store_name] and are protected by international copyright, trademark, patent, trade secret, and other intellectual property or proprietary rights laws.<br><br>Links to Other Websites<br>Our Website may contain links to third-party websites or services that are not owned or controlled by [store_name]. We have no control over, and assume no responsibility for, the content, privacy policies, or practices of any third-party websites or services. You further acknowledge and agree that [store_name] shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with the use of or reliance on any such content, goods, or services available on or through any such websites or services.<br><br>Limitation of Liability<br>In no event shall [store_name] be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses, resulting from (i) your use or inability to use the Website; (ii) any unauthorized access to or use of our servers and\/or any personal information stored therein; (iii) any interruption or cessation of transmission to or from the Website; (iv) any bugs, viruses, trojan horses, or the like that may be transmitted to or through the Website by any third party; (v) any errors or omissions in any content or for any loss or damage incurred as a result of the use of any content posted, emailed, transmitted, or otherwise made available through the Website.<br><br>Governing Law<br>These Terms shall be governed and construed in accordance with the laws of [store_jurisdiction], without regard to its conflict of law provisions.<br><br>Changes to Terms and Conditions<br>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. Any changes will be effective immediately upon posting the updated Terms on the Website. Your continued use of the Website after such changes constitutes your acceptance of the new Terms.<br><br>Contact Us<br>If you have any questions about these Terms and Conditions, please contact us at [store_email]."}'
        ),
        array(
            "id" => 67,
            "term_id" => 27,
            "key" => "meta",
            "value" => '{"page_excerpt":"","page_content":"Last updated: [Date]<br><br>Thank you for shopping at [store_name]. We value your satisfaction and strive to ensure that every purchase meets your expectations. If you are not entirely satisfied with your purchase, we\'re here to help.<br><br> Returns<br> You have 30 days to return an item from the date you received it. To be eligible for a return, your item must be unused, in the same condition as you received it, and in the original packaging.<br> <br>Refunds<br>Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item.<br><br>If your return is approved, we will initiate a refund to your original method of payment. You will receive the credit within a certain amount of days, depending on your card issuer\'s policies.<br><br>Exchanges<br>If you wish to exchange an item for another, please contact us to make arrangements. Exchanged items will be shipped to you once the original item has been received, inspected, and approved for return.<br><br>Shipping<br>You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.<br><br>Contact Us<br>If you have any questions on how to return your item to us, contact us at [store_email].<br><br>Exceptions<br>Please note that the following items are generally not eligible for return:<br><br>Personalized or customized items.<br>Perishable goods, such as food or flowers.<br>Intimate or sanitary goods, such as personal care products.<br> Downloadable digital products.<br> Gift cards.<br> Damaged or Defective Items<br> If you receive a damaged or defective item, please contact us immediately. We will work with you to resolve the issue promptly.<br> <br> Change of Mind<br> If you simply change your mind about a purchase and wish to return it, we may accept the return at our discretion. In such cases, a restocking fee may apply.<br> <br> Final Sale Items<br> Certain items marked as \"Final Sale\" or \"Non-Returnable\" cannot be returned or exchanged.<br> <br> Policy Updates<br> We reserve the right to update or change our Return Policy at any time without prior notice. Any changes will be posted on this page, and the revised policy will apply to all purchases made after the date of the change.<br> <br> By making a purchase, you agree to the terms of our Return Policy as stated on this page."}'
        )
    );
        Termmeta::insert($metas);
    }
}
