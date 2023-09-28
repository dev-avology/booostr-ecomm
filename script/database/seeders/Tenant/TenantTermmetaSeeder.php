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
            "value" => '{"page_excerpt":"","page_content":"Last updated: [Date]<br><br>Thank you for shopping at [store_name]. We value your satisfaction and strive to ensure that every purchase meets your expectations. If you are not entirely satisfied with your purchase, we\'re here to help.<br><br>Returns<br>You have [X days\/weeks] to return an item from the date you received it. To be eligible for a return, your item must be unused, in the same condition as you received it, and in the original packaging.<br><br>Refunds<br>Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item.<br><br>If your return is approved, we will initiate a refund to your original method of payment. You will receive the credit within a certain amount of days, depending on your card issuer\'s policies.<br><br>Exchanges<br>If you wish to exchange an item for another, please contact us to make arrangements. Exchanged items will be shipped to you once the original item has been received, inspected, and approved for return.<br><br>Shipping<br>You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.<br><br>Contact Us<br>If you have any questions on how to return your item to us, contact us at [store_email].<br><br>Exceptions<br>Please note that the following items are generally not eligible for return:<br><br>Personalized or customized items.<br>Perishable goods, such as food or flowers.<br>Intimate or sanitary goods, such as personal care products.<br>Downloadable digital products.<br>Gift cards.<br>Damaged or Defective Items<br>If you receive a damaged or defective item, please contact us immediately. We will work with you to resolve the issue promptly.<br><br>Change of Mind<br>If you simply change your mind about a purchase and wish to return it, we may accept the return at our discretion. In such cases, a restocking fee may apply.<br><br>Final Sale Items<br>Certain items marked as \"Final Sale\" or \"Non-Returnable\" cannot be returned or exchanged.<br><br>Policy Updates<br>We reserve the right to update or change our Return Policy at any time without prior notice. Any changes will be posted on this page, and the revised policy will apply to all purchases made after the date of the change.<br><br>By making a purchase, you agree to the terms of our Return Policy as stated on this page."}'
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
            "value" => '{"page_excerpt":"","page_content":"Last updated: [Date]<br><br>Thank you for shopping at [store_name]. We value your satisfaction and strive to ensure that every purchase meets your expectations. If you are not entirely satisfied with your purchase, we\'re here to help.<br><br> Returns<br> You have [X days\/weeks] to return an item from the date you received it. To be eligible for a return, your item must be unused, in the same condition as you received it, and in the original packaging.<br> <br>Refunds<br>Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item.<br><br>If your return is approved, we will initiate a refund to your original method of payment. You will receive the credit within a certain amount of days, depending on your card issuer\'s policies.<br><br>Exchanges<br>If you wish to exchange an item for another, please contact us to make arrangements. Exchanged items will be shipped to you once the original item has been received, inspected, and approved for return.<br><br>Shipping<br>You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.<br><br>Contact Us<br>If you have any questions on how to return your item to us, contact us at [store_email].<br><br>Exceptions<br>Please note that the following items are generally not eligible for return:<br><br>Personalized or customized items.<br>Perishable goods, such as food or flowers.<br>Intimate or sanitary goods, such as personal care products.<br> Downloadable digital products.<br> Gift cards.<br> Damaged or Defective Items<br> If you receive a damaged or defective item, please contact us immediately. We will work with you to resolve the issue promptly.<br> <br> Change of Mind<br> If you simply change your mind about a purchase and wish to return it, we may accept the return at our discretion. In such cases, a restocking fee may apply.<br> <br> Final Sale Items<br> Certain items marked as \"Final Sale\" or \"Non-Returnable\" cannot be returned or exchanged.<br> <br> Policy Updates<br> We reserve the right to update or change our Return Policy at any time without prior notice. Any changes will be posted on this page, and the revised policy will apply to all purchases made after the date of the change.<br> <br> By making a purchase, you agree to the terms of our Return Policy as stated on this page."}'
        )
    );
        Termmeta::insert($metas);
    }
}
