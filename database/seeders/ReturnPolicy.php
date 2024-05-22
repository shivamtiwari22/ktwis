<?php

namespace Database\Seeders;

use App\Models\ReturnPolicy as ModelsReturnPolicy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnPolicy extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelsReturnPolicy::create([
            'subject' => 'Un categorized',
            'message' => 'Certainly! Creating a return policy for an e-commerce store is essential to set clear expectations for your customers and establish trust. Below is a template you can use as a starting point for your return policy:

                ---
            
                **Return Policy for Ktwis**
                
                
                
                
                
                Thank you for shopping with Ktwis. We value your satisfaction and want to ensure that your shopping experience with us is enjoyable. Please read our return policy carefully to understand your options for returns.
                
                
                
                **1. Returns**
                
                
                
                1.1 We accept returns within 5 days of the purchase date.
                
                
                
                1.2 To be eligible for a return, the item must be unused and in the same condition as received. It must also be in the original packaging.
                
                
                
                **2. Exchanges**
                
                
                
                2.1 If you receive a defective or damaged item, please contact our customer service within 5 days of receiving the product. We will happily exchange the item for a new one.
                
                
                
                **3. Refunds**
                
                
                
                3.1 Once your return is received and inspected, we will send you an email to notify you that we have received your returned item. We will also notify you of the approval or rejection of your refund.
                
                
                
                3.2 If your return is approved, a refund will be processed, and a credit will automatically be applied to your original method of payment within 10 business days.
                
                
                
                **4. Return Shipping**
                
                
                
                4.1 Customers are responsible for the cost of return shipping unless the return is due to a defect or an error on our part.
                
                
                
                4.2 We recommend using a trackable shipping service and purchasing shipping insurance for returns. We cannot guarantee that we will receive your returned item.
                
                
                
                **5. Non-Returnable Items**
                
                
                
                5.1 Certain items are non-returnable, including [list specific items or categories].
                
                
                
                **6. How to Initiate a Return**
                
                
                
                6.1 To initiate a return, please contact our customer service team at [customer service email] with your order number and details about the product you would like to return.
                
                
                
                **7. Cancellations**
                
                
                
                7.1 If you wish to cancel your order, please contact us as soon as possible. Once an order has been shipped, it cannot be canceled.
                
                
                
                **8. Contact Information**
                
                
                
                8.1 If you have any questions about our return policy, please contact us at [customer service email].
         
                ---
                Feel free to customize this template based on your specific business needs, industry regulations, and the types of products you sell. Always ensure that your return policy is easily accessible on your website for customers to review before making a purchase.',
            'category_id' => '0',


         
        ]);
    }
}
