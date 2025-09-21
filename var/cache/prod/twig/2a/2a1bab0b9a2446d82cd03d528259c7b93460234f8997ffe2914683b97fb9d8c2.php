<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* credit-report.html.twig */
class __TwigTemplate_69fbb44f5608e5bb472a86bbe075608046271b8ce17101b412948dab70ecf4e2 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<style type=\"text/css\">
@page {
   size: 7in 9.25in;
   margin: 6mm 8mm 6mm 8mm;
}

#wrapper {
    with: 100%;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 
}

p {
    font-size: 11pt;
}

.size-100 {
    width: 100%;
    text-align: center;
}

.credit-graph {
    width: 250px;
    height: 145px;
    background: url('images/credit-rainbow.png') center no-repeat;

    text-align: center;
    vertical-align: bottom;
}

.page-break {
    page-break-after: always;
}

table {
    border-radius: 5px;
    border: 1px solid #c3c3c3;
    box-shadow: 2px 2px #CCC;
}

thead th {
    border-bottom: none;
    padding: 8px;
}

tbody td {
    border: 1px dashed #c3c3c3;
    padding: 8px;
}

.odd-color {
    background: #F1F1F1;
}

.border-none {
    border: none;
    box-shadow: none;
}

@media print {

}

</style>
<div id=\"wrapper\">
    <div class=\"size-100\" style=\"padding-top: 100px;\">
        <img src=\"images/logo-new.png\" width=\"500\" />

        <h4>Credit Analysis Report</h4>

        <p><i>Prepared for</i></p>

        <h3>";
        // line 73
        echo twig_escape_filter($this->env, ($context["first_name"] ?? null), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, ($context["last_name"] ?? null), "html", null, true);
        echo "</h3>

        <p>";
        // line 75
        echo twig_escape_filter($this->env, ($context["report_date"] ?? null), "html", null, true);
        echo "</p>
        <br/><br/>

    </div>


    <div class=\"size-100\">
        <p>
            <i>Prepared by</i><h4>Frantz Chery</h4>Top Tier Financial Solutions<br/>2141 Cortelyou Road<br/>Brooklyn, NY 11226<br/>info@toptierfinancialsolutions.com<br/>Office:(800) 478-7119<br/>Phone: (347) 699-4664<br/>Fax: (718) 489-4145<br/>https://www.toptierfinancialsolutions.com
        </p>
    </div>

    <div class=\"page-break\">&nbsp;</div>

    <strong>Dear ";
        // line 89
        echo twig_escape_filter($this->env, ($context["first_name"] ?? null), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, ($context["last_name"] ?? null), "html", null, true);
        echo "</strong>

    <p>On behalf of Top Tier Financial Solutions, I'd like to take this opportunity to welcome you as a new client! We
    are thrilled to have you with us.</p>

    <p>First and foremost, we would like to thank you for the opportunity to work on your file. Without our clients, our days would be horribly boring! So, before we get too deep into the audit on your personal credit file, I wanted to just take a moment and bury into your mind this one simple thought. NO MATTER where your credit is at the moment, good or bad, that we are here to ensure that your credit education and the journey is a pleasant one. And also to point out that... Bad credit ISN’T funny!</p>

    <p>Some things to note regarding your audit:</p>
    
    <ol>
        <li>This information is YOURS and based on YOUR credit profile</li>
        <li>This information is based on YEARS of research by our company in the credit industry</li>
        <li>There will be tasks for both YOU and Top Tier Financial Solutions inside this audit</li>
        <li>Please take the time to educate yourself on your credit</li>
    </ol>
    
    <p>This credit analysis report provides an overview of your credit as potential lenders see it today. It lists the items
    that are negatively affecting your score and explains how we use the power of the law to improve your credit. It
    also includes a simple step-by-step plan for you to speed up the process.</p>

    <p>This credit analysis report is broken down into the following 5 sections:</p>

    <ol>
        <li>Credit Score Basics</li>
        <li>Your Credit Scores and Summary</li>
        <li>Analysis of Your Accounts</li>
        <li>An Overview of Our Process</li>
        <li>Your Part in the Process</li>
    </ol>
    
    <p>Our team here at Top Tier Financial Solutions will be here every step of the way. After your audit, we will start working on your file based on the data we have obtained during the audit process. Once we complete your file, you will have several opportunities to continue to work with us regarding your credit. We offer an array of other services that consist of debt assumption, funding programs, etc. So please if you think of anything that you may need, consider us first.</p>

    <p>Please let us know if you have any questions along the way. I can assure you that our mission is now and always will be helping you reach your goals. If there is anything you need, do not hesitate to ask.</p>

    <ul>
        <li>Email: info@toptierfinancialsolutions.com</li>
        <li>Office: (800) 478-7119</li>
        <li>Phone: (347) 699-4664</li>
        <li>Fax: (718) 489-4145</li>
        <li>Website: https://www.toptierfinancialsolutions.com</li>
    </ul>

    <p>";
        // line 131
        echo twig_escape_filter($this->env, ($context["first_name"] ?? null), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, ($context["last_name"] ?? null), "html", null, true);
        echo ", thank you again for entrusting Top Tier Financial Solutions to restore your credit. We are
    honored to help you achieve your financial goals.</p>

    <p>Best,</br>Frantz Chery</p>

    
    <h5>Scope of the Work</h5>
    <p>In the audit of the subject report, the auditor completed the following steps and analyses:</p>

    <ol>
        <li>Gathered and analyzed data on the report. </li>
        <li>In connection with this audit, the auditor may or may not have obtained information from:  </li>
    </ol>
    <ul>
        <li>Credit Reporting Agency</li>
        <li>Consumer</li>
        <li>Creditors of the Consumer</li>
    </ul>
    
    <h5>INTENDED USE AND INTENDED USERS OF THE AUDIT</h5>
    <p>The intended use of the audit is to provide support and knowledge in respect to the consumer data provided. The intended user of the report is our client, their counsel, and the opposing side of any litigation, their counsel, and the court. This audit is prepared for the sole and exclusive use of the identified intended users. No third parties are authorized to rely upon this report without the express written consent of the auditor.</p>

    
    <h5>ASSUMPTIONS AND LIMITING CONDITIONS</h5>
    <p>The Summary of Audit at the conclusion of this report is subject to the following conditions and to other specific and limiting conditions as described by the auditor in the report. Again, this summary can in no means be construed as legal or financial advice.</p>

    <ol>
        <li>We assume no responsibility for matters legal in nature affecting the report appraised.</li>
        <li>We assume no responsibility for their accuracy of the credit report provided to our company.</li>
        <li>Full compliance with all applicable federal, state and local environmental regulations and laws is assumed unless noncompliance is stated, defined and considered in the audit report.</li>
        <li>We assume there are no hidden or unapparent conditions of the report that would render the accounts as more or less damaging to the credit score. We assume no responsibility for such conditions.</li>
        <li>No requirement shall be made of the auditor to appear in court by reason of this audit of the report in question.</li>
        <li>Possession of this report, or copy hereof, does not carry with it the right of publication nor may it be used for any purposes whatsoever by any party but those stated herein without the previous written consent of the auditor or the auditor’s client.</li>
    </ol>

   <div class=\"page-break\">&nbsp;</div>
    
    <h4>PART 1 - CREDIT SCORE BASICS</h4>

    <p><strong>What a Low Credit Score Costs you</strong></p>

    <table style=\"text-align: center\" class=\"size-100 border-none\">
        <tr>
            <td colspan=\"2\" class=\"border-none\">
                <img src=\"images/toyota-camery.png\" /></br>
                Brand New Toyota Camery</br>
                \$23,000</br>
                66 month term
            </td>
        </tr>
        <tr>
            <td class=\"border-none\">
                Person A</br>
                Credit Score: 730</br>
                Interest Rate: 1.99% </br>
                Payment: \$368.22 </br>
                Total Interest Paid \$1302.39 </br>
                Total Payments: \$24,302.39</br>
            </td>
            <td class=\"border-none\">
                Person B</br>
                Credit Score: 599</br>
                Interest Rate: 14.99%</br>
                Payment: \$513.97</br>
                Total Interest Paid \$10,921.44</br>
                Total Payments: \$33,921.44
            </td>
        </tr>
        <tr>
            <td colspan=\"2\"  class=\"border-none\">
                <p>Person B pays more</p>
                <h4>\$9,616.05 MORE</h4>
                <p>than person A for the exact same car and price!</p>
                <p>This same thing happens with your credit cards, mortgage, loans, etc.</p>
                <p>Cleaning up your credit will lower your bills</br>and can save hundreds of thousands of dollars!</p>
            </td>
        </tr>
    </table>

    <div class=\"page-break\">&nbsp;</div>

    <h5>What Is A Credit Score?</h5>

    <p>
    A credit score is a number that attempts to predict your “credit-worthiness” at any given moment. Officially, it’s supposed to predict how likely you are to become at least 90 days late on payments within the next twenty-four months. Credit scores are calculated using complex, secret formulas that are only known by the companies that produce them (although these companies have given us some general guidance on how they calculate credit scores.) A company called Fair Isaac Corporation pioneered the use of credit scores in 1956, but they didn’t become widely used by creditors until the 1980’s. Then, in 1995, Fannie Mae and Freddie Mac recommended the use of credit scores in mortgage lending. From then on, credit scores became perhaps the single-most important tool for creditors when offering loans to consumers. Now, credit scores are even used by insurance companies and other service providers in determining whether, and on what terms, they will offer their services to you. There are now many different types of credit scores, developed by different companies, for use in different industries. For example, there are credit scores that are used solely for automotive lenders, credit card issuers, or finance companies. Some commentators have suggested that, between the different credit scoring companies, there are more than 1000 different credit-scoring models currently in use. But, the most widely used credit score, by far, is the score developed by the Fair Isaac Company known as a “FICO” score. To determine a FICO score for a consumer, Fair Isaac developed a formula based on nearly forty different “characteristics” that it claims predicts the likelihood that the consumer will repay their debts. Fair Isaac also groups different classes of consumers according to key “attributes” and then compares a given consumer’s credit file to other consumers in that same group. For example, there may be a group of consumers who have filed for bankruptcy. There may be another group of consumers who have one late payment, and so on. Fair Isaac believes that separating consumers into groups of consumers with common key attributes makes the credit score even more predictive of credit worthiness. This system is called a “scorecard” system. But guess what? There’s more than one credit-scoring model. Sure, there are other companies that have their own credit scoring system (we’ll talk about that in a minute). But, even Fair Isaac has more than one credit-scoring model. In fact, they have many different credit scoring models. The most commonly used model is known as the “Classic” FICO scoring model. This model uses 10 “scorecards” or groups of people with similar key attributes. But, Fair Isaac has also developed another scoring model called “Next Generation” or “NextGen” that uses 18 scorecards or groups. Fair Isaac believes that the NextGen scoring model is even more advanced and predictive than the Classic model. In addition, Fair Isaac has developed enhancements to the Classic model. So, why should you care about this? Well, it’s a problem for us every day, because different creditors use different credit scoring models. Some may use the Classic FICO. Others may use the enhanced versions of the Classic FICO. Still others may use the NextGen FICO. And the result? Yes, that’s right, different scores. A lender may pull a credit score for a potential borrower, and get one score. But, if another lender uses a different credit reporting company for their credit report, it’s very possible that the credit score will be different. So, while the borrower would qualify for a certain loan based on one credit report and score, the borrower may NOT qualify using the other credit report. 
    </p>

    <h5>What Is A Credit Bureau?</h5>
    <p>
    A credit bureau is a company that collects and maintains your credit information and sells it to lenders, creditors
    and consumers in the form of a credit report. There are dozens of credit bureaus, we're most concerned with
    the big three: Equifax, Experian, and TransUnion.
    </p>

    <h5>What is a credit report?</h5>
    <p>
    You are the source of great interest and profit for many national companies. These companies, known as “consumer reporting agencies” under the Fair Credit Reporting Act, gather financial and personal information about you and millions of other consumers across the country. They use this information to make a profit – that’s right; they sell your information to make a buck. The FCRA defines a consumer-reporting agency as: Any person which, for monetary fees, dues, or on a cooperative nonprofit basis, regularly engages in whole or in part in the practice of assembling or evaluating customer credit information or other information on consumers for the purpose of furnishing consumer reports to third parties, and which uses any means or facility of interstate commerce for the purpose of preparing or furnishing consumer reports. These consumer-reporting agencies sell your information to businesses and individuals with a “legitimate” need to know something about you. Most often, these businesses are creditors that want to lend you money or otherwise provide you with credit. But, the consumer reporting agencies have many customers that are eager for your information, including prospective employers, landlords, and insurance companies. All of these businesses and individuals are trying to decide whether you’re a “good risk” for whatever it is they’re offering to you. If your credit report contains mostly “good” information, then you’ll probably receive the loan or service. But, if your credit report contains mostly “bad” information, then you’re probably not going to get the loan or service. We’re going to closely examine the information that’s contained in your credit reports in a moment, and then we’re going to talk about how to fix that information. But for now, let’s take a close look at the major consumer reporting agencies. There are currently three major credit bureaus (consumer reporting agencies) that collect, maintain and report general credit information regarding consumers– Equifax, Experian (formerly TRW), and TransUnion. These companies now each maintain credit information on more than 200 million consumers nationwide.
    </p>

    <h5>What's Included in Your Credit Report</h5>
    <p>
    Personal Data:<br/>
    This section has identifying and employment information. It could include full name, spouses name, current and former address, date of birth, current and former employers. Although the personal data section does not directly affect the credit score, it is important that this section is correct for identifying purposes. Suffixes, such as Sr., Jr., III etc., often get mixed up. Father and son with the same name will often get their information reported on each other’s credit reports.
    </p>
    <p>
    Public Records:<br/>
    The public information section of the credit report includes publicly available information about legal matters affecting your client’s credit. This could include judgments in civil actions, state or federal tax liens and bankruptcies. All court records, including satisfactions, are considered negative by all credit grantors.
    </p>
    <p>
    Accounts/Trade lines:<br/>
    This includes credit cards, auto loans, mortgages, real estate, installment loans and revolving debt like department store cards. The report will include information on the accounts such as the balance, payment history, terms, and account status such as whether the account was put into bankruptcy, charged off, or repossessed.
    </p>
    <p>
    Collection Accounts:<br/>
    Collections are accounts that are seriously past due and have been transferred to a collection agency or creditor's internal collection department. Collections can appear to be paid and unpaid (and, yes this makes a difference when disputing...more on this later). Any type of collection whether it is paid or unpaid it is negative. One thing you may encounter is multiple listings on credit reports for the same debt. This happens as the debt collection agencies sell the debt to other agencies. As debt is transferred between different agencies, there may be several records on the credit report for the same debt. Only one record should be marked as open at a time on the credit report.
    </p>
    <p>
    Inquiries:<br/>
    Every time credit is applied for, a credit report is pulled. The inquiry section of a credit report includes records of businesses that have checked your credit in the last two years. When creditors and lenders review clients’ credit data for the purpose of an application, a hard inquiry is listed on the credit report. Too many hard inquiries can harm a credit score. The reason being is that credit grantors get nervous that they are not managing credit “responsibly.”
    </p>

    <h5>What's Not Included In Your Credit Reports?</h5>

    <p>
    Contrary to popular belief, your credit report does not contain information about your checking or savings accounts, or your race, religion, gender, political affiliation, or personal lifestyle. Your credit report also does not contain medical history or criminal records. But, remember, your credit report is only one type of consumer report. As we've seen, there are specialized consumer reporting agencies that DO collect, maintain, and report some of this information). In addition, there are companies that provide what the FCRA calls an “investigative consumer report” which it defines as: A consumer report or portion thereof in which information on a consumer's character, general reputation, personal characteristics, or mode of living is obtained through personal interviews with neighbors, friends, or associates of the consumer reported on or with others with whom he is acquainted or who may have knowledge concerning any such items of information. Fortunately, the FCRA requires that these companies notify you if they prepare an investigative consumer report about you. Although this is something to certainly worry about, let's stay focused on credit reports for now. Just remember that credit reports and investigative consumer reports are completely different. Finally, your credit report might also not contain information from all of your creditors. As we talked about above, some of your creditors may not report to all of the credit bureaus, and some may not report to any credit bureau. Therefore, parts of your credit accounts and history may appear in different credit reports, or not at all.
    </p>

    <h5>Where Does the Information Come From?</h5>
    <p>
    Now that you know who creates the credit reports, let’s talk about where the information comes from.
    </p>
    <p>
    You:<br/>
    Yes, you! You unknowingly supply a great deal of information to the credit bureaus. How? Generally this happens when you apply for credit. When you apply for credit, you typically complete a credit application in which you supply your full name, Social Security Number, current and former addresses, and current and previous employment. And guess what your potential creditor does with the information you listed in the application? That’s right. They send it all to the credit bureaus. This information then becomes a part of your credit file. Therefore, it’s important that you accurately complete this information on any credit applications the same way EVERY TIME!
    </p>
    <p>
    Your Creditors:<br/>
    Your current and former creditors also provide information to the credit bureaus about you. These creditors tell the credit bureaus how you’ve paid your bills each month. But, not all of your creditors report all of your payment history to the credit bureaus. Most creditors, sometimes called “automatic subscribers,” report all of your payment history to the credit bureaus every month. Other creditors, sometimes called “limited subscribers” only report certain types of information – like delinquencies.
    </p>
    <p>
    Automatic Subscribers:<br/>
    Automatic Subscribers are creditors that regularly report information to the credit bureaus about your account with them. This information generally includes the date when this creditor opened the account, the total amount of the debt or credit limit, the current balance, and your payment history – good or bad. Just because a creditor is an automatic subscriber to one credit bureau, doesn’t necessarily mean that the creditor will report to all of the major credit bureaus. That’s one reason that the credit reports produced by different credit bureaus very often contain different information.
    </p>
    <p>
    Limited Subscribers:<br/>
    Limited Subscribers are creditors that do NOT regularly report information to the credit bureaus. Instead, these creditors may only report certain types of information – like delinquencies or collection activities. They generally do not report good credit information, usually just bad information. There are many different types of limited subscribers, including apartment management companies, insurance companies, utility companies, medical providers, and collection agencies. As with automatic subscribers, many limited subscribers may only report information to one of the national credit bureaus. Therefore, bad information reported by a limited subscriber may only affect one of your credit reports.
    </p>
    <p>
    Creditors That Do Not Report to Any Credit Bureau:<br/>
    Finally, there are some creditors that do not report to the credit bureaus AT ALL. This means that any information – good or bad – will not show in any of your credit reports. Typically, these creditors include individuals, like landlords, or small companies. (When you’re trying to improve your credit, you need to be aware of creditors that do not report to the credit bureaus. These creditors will NOT help you to restore your credit.)
    </p>

    <h5>Who Can See Your Credit Reports</h5>
    <p>
    Under section 604 of the FCRA (15 U.S.C. section1681b), only certain people may access your credit report, and then only for certain specified reasons. Generally, this means prospective creditors. However, there are many reasons that someone may access your credit report. For example, with restrictions, a potential employer may access your credit report when you apply for job. To get a sense for this, let’s look at the list of “Permissible Purposes of Consumer Reports” as set forth in section 604 of the FCRA to find out who can see your credit report. 1. A court or federal grand jury; 2. You; 3. Someone (a creditor) that intends to use the information in connection with a credit transaction; 4. Other individuals or companies that intend to use the information in connection with employment, the underwriting of insurance, or certain government benefits; 5. Other individuals or companies that have a legitimate business need for the information in connection with a business transaction initiated by you; 6. A state or local child support enforcement agency; 7. An agency administering a State plan under section 454 of the Social Security act to set an initial or modified child support award.
    </p>

    <h5>How Are Credit Scores Calculated?</h5>

    <p>
        <img src=\"images/credit-graph.png\" />
    </p>
    <p>
    Fair Isaac and Vantage Score hold their credit scoring formulas as a close secret much like the formula for Coca-Cola or your grandma’s legendary double chocolate-chip cookies. This can be very frustrating for consumers when they see remarks on the credit report like “too many revolving debt accounts” and not knowing exactly that means. We will go over the scoring factors for your FICO score.
    </p>
    
    <h5>Payment History - 35%</h5>
    <p>
    The top rated factor is payment history. This is because lenders want to know a person’s payment habits both past and present. This category can be broken down into three subcategories: Recency – This is the last time a payment was late. The more time that passes the better. Frequency – One late payment looks a heck of a lot better than a dozen lates in a row. Severity – This rests on the logic that a payment 30 days late is not as serious as a payment 60 or 120-days late.   ALERT!   REMEMBER - Collections, tax liens and bankruptcies are credit score killers.
    </p>
    
    <h5>Utilization Ratio: 30%</h5>
    <p>
    The score looks at the total amount owed on all accounts as well as how much you owe on different types of accounts especially REVOLVING accounts. Using a higher percentage of the credit limits will worry lenders and hurt the credit score. People who max out their limits have a much greater risk of default. When it comes to revolving debt-credit cards, the formula looks at the difference between the high limit and balances. For Example, let’s say you have a MasterCard with a credit limit of \$10,000 and you have spent \$2,000 of it. This is a 20% utilization ratio. The lower the ratio, the higher the credit score. So, pay down any revolving accounts you can. Don’t expect this to be instantaneous as it can take up to 45 days for the bureaus to update reports.
    </p>
    
    <h5>Age of File – 15%</h5>
    <p>
    This is less important than the previous factors, but it still matters. It considers (1) the age of the oldest account and (2) the average age of all your open accounts. It is possible to have a good score with a short history, but typically the longer the better. Young people, students, and others can still have high credit scores as long as the other factors are positive. If a person is new to credit then there is little they can do to improve a credit score. The only solution is to open an account and be patient.
    </p>
    
    <h5>Mix of Credit – 10%</h5>
    <p>
    Both models want to see a healthy mix of credit, but they are vague on what this means. They recommend you have a balance of both revolving debts like consumer credit cards and installment loans like auto loans or a mortgage. Usually, a good report will contain a mortgage, 1-2 auto loans, 3-5 credit cards, and some personal debt loans such as signature or student loans.
    </p>
    
    <h5>Inquiries/ Recent Credit: 10%</h5>
    <p>
    New credit is not always a bad thing. However, opening new accounts can hurt a credit score, particularly if a consumer applies for lots of credit in a short time and doesn’t have a long credit history. <br/>The score factors in the following: <br/>1. How many accounts has the consumer applied for recently? <br/>2. How many new accounts the consumer has opened? <br/>3. How much time has passed since the consumer applied?<br/>4. How much time has passed since the account was opened?<br/><br/>The model looks for “rate shopping.” Shopping for a mortgage or an auto loan may cause multiple lenders to request your credit report many times each, even though a person is only looking for one loan. Auto dealers are notorious for running 3 to 15 credit reports. This is called shot gunning the credit. Luckily, to compensate for this, the score counts multiple inquiries in any 14-day period as just one inquiry. For most people, a credit inquiry will take less than five points off their score. However, inquiries can have a greater impact if you have few accounts or a short credit history. Large numbers of inquiries also mean greater risk. According to MyFico.com, people with six inquiries or more on their credit reports are eight times more likely to declare bankruptcy than people with no inquiries on their reports.
    </p>
    

    <h5>How Your Behavior Is Evaluated In Your Credit Report:</h5>

    <p>
    <strong>Do you pay your bills on time?</strong> Payment history is a major factor in credit scoring. If you have paid bills late, have collections or a bankruptcy, these events won't reflect well in your credit score.
    </p>
    <p>
    <strong>Do you have a long credit history?</strong> Generally speaking, the longer your history of holding accounts is, the more trusted you will be as a borrower
    </p>
    <p>
    <strong>Have you applied for credit recently?</strong> If you have many recent inquiries this can be construed as being negative by the bureaus. Only apply for credit when you really want it.
    </p>
    <p class=\"page-break\">
    <strong>What is your outstanding debt?</strong> It is important to not use all of your available credit. If all of your credit cards are maxed out, your scores will reflect that you are not managing your debt wisely. 
    </p>


    <h5>Credit Score Ranges And Their Meaning</h5>
    <p><img src=\"images/credit-bar.png\" /></p>
    <p>
    <strong>800 and Higher</strong> (Excellent) With a credit score in this range no lender will ever disapprove your loan application. Additionally, the APR (Annual Percentage Rate) on your credit cards will be the lowest possible. You'll be treated as royalty. Achieving this excellent credit rating not only requires financial knowledge and discipline, but also a good credit history. Generally speaking, to achieve this excellent rating you must also use a substantial amount of credit on an ongoing monthly basis and always repay it ahead of time.
    </p>
    <p>
    <strong>700 - 799</strong> (Very Good) 27% of the United States population belongs to this credit score range. With this credit score range, you will enjoy good rates and approved for nearly any type of credit loan or personal loan, whether unsecured or secured.
    </p>
    <p>
    <strong>680 - 699</strong> (Good) This range is the average credit score. In this range approvals are practically guaranteed but the interest rates might be marginally higher. If you're thinking about a long term loan such as a mortgage, try working to increase your credit score higher than 720 and you will be rewarded for your efforts; your long term savings will be noticeable.
    </p>
    <p>
    <strong>620 - 679</strong> (OK or Fair) Depending on what kind of loan or credit you are applying for and your credit history, you might find that the rates you are quoted aren't best. That doesn't mean that you won't be approved but, certain restrictions will apply to the loan's terms.
    </p>
    <p>
    <strong>580 - 619</strong> (Poor) With a poor credit rating you can still get an unsecured personal loan and even a mortgage, but the terms and interest rates won't be very appealing. You'll be required to pay more over a longer period of time because of the high interest rates.
    </p>
    <p>
    <strong>500 - 579</strong> (Bad) With a score in this range you can get a loan but nothing even close to what you expect it to be. Some people with bad credit apply for loans to consolidate debt in search for a fresh start. However, if you decide to do that proceed cautiously. With a 500 credit score you need to make sure that you don't default on payments or you'll be making your situation worse and might head towards bankruptcy, which is not what you want.
    </p>
    <p>
    <strong>499 and Lower</strong> (Very Bad) If this is your score range you need serious assistance with how you handle your credit. You're making too many credit blunders and they will only get worse if you don't take positive action. If you are thinking of a loan (which won't be easy), the rates will be very high and the terms will be very strict. We recommend that you fix your credit first before applying for a loan.
    </p>

    <div class=\"page-break\">&nbsp;</div>

    <h3>PART 2 - YOUR CREDIT SCORES AND SUMMARY</h3>
    <p>
        We have analyzed your credit reports from the three major bureaus. Here are our findings:
    </p>
    <h4>Your Credit Scores:</h4>
    <table class=\"size-100\">
        <thead>
            <tr>
                <th>
                    EQUIFAX
                </th>
                <th>
                    TRANSUNION
                </th>
                <th>
                    EXPERIAN
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class=\"credit-graph\">
                    <h3>";
        // line 387
        echo twig_escape_filter($this->env, ($context["equifax_credit_score"] ?? null), "html", null, true);
        echo "</h3>
                </td>
                <td class=\"credit-graph\">
                    <h3>";
        // line 390
        echo twig_escape_filter($this->env, ($context["transunion_credit_score"] ?? null), "html", null, true);
        echo "</h3>
                </td>
                <td class=\"credit-graph\">
                    <h3>";
        // line 393
        echo twig_escape_filter($this->env, ($context["experian_credit_score"] ?? null), "html", null, true);
        echo "</h3>
                </td>
            </tr>
        </tbody>
    </table>
    
    <h3>Keep Your Credit Monitoring Account Active Throughout The Credit Repair Process</h3>

    <p>
        Your credit scores may vary depending on where you get your credit reports from, because different sources have different methods for determining your score and scheduling updates. Maintaining one (1) credit monitoring account will give us a baseline score as a point of reference to grow from, to accurately see changes as they happen. For this reason, you must keep your same credit monitoring account active, rather than checking your scores on multiple sites that will differ.
    </p>

    <h3>Your Derogatory Summary:</h3>

    <p>Next we analyzed all the items on your reports, to determine which accounts are negatively impacting your score. Here are our findings:</p>

    <table class=\"size-100\">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>EQUIFAX</th>
                <th>EXPERIAN</th>
                <th>TRANSUNION</th>
            </tr>
        </thead>
        <tbody>
            <tr class=\"odd-color\">
                <td>Delinquent:</td>
                <td>";
        // line 421
        echo twig_escape_filter($this->env, ($context["equifax_delinquent"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 422
        echo twig_escape_filter($this->env, ($context["experian_delinquent"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 423
        echo twig_escape_filter($this->env, ($context["transunion_delinquent"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Derogatory:</td>
                <td>";
        // line 427
        echo twig_escape_filter($this->env, ($context["equifax_derogatory"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 428
        echo twig_escape_filter($this->env, ($context["experian_derogatory"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 429
        echo twig_escape_filter($this->env, ($context["transunion_derogatory"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr class=\"odd-color\">
                <td>Collection:</td>
                <td>";
        // line 433
        echo twig_escape_filter($this->env, ($context["equifax_collection"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 434
        echo twig_escape_filter($this->env, ($context["experian_collection"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 435
        echo twig_escape_filter($this->env, ($context["transunion_collection"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Public Records:</td>
                <td>";
        // line 439
        echo twig_escape_filter($this->env, ($context["equifax_public_records"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 440
        echo twig_escape_filter($this->env, ($context["experian_public_records"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 441
        echo twig_escape_filter($this->env, ($context["transunion_public_records"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr class=\"odd-color\">
                <td>Inquiries(2 years):</td>
                <td>";
        // line 445
        echo twig_escape_filter($this->env, ($context["equifax_inquiries"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 446
        echo twig_escape_filter($this->env, ($context["experian_inquiries"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 447
        echo twig_escape_filter($this->env, ($context["transunion_inquiries"] ?? null), "html", null, true);
        echo "</td>
            </tr>                                              
        </tbody>
    </table>

    <div class=\"page-break\">&nbsp;</div>

    <h4>PART 3 - ANALYSIS OF YOUR ACCOUNTS</h4>

    <h5>Your Derogatory Items:</h5>

    <p>You have <strong>";
        // line 458
        echo twig_escape_filter($this->env, ($context["derogatory_accounts_total"] ?? null), "html", null, true);
        echo "</strong> items marked as delinquent or derogatory. Recent late payments, collections, and other
derogatory items within the last six months will hurt your credit score more than older inactive accounts.
Accounts within the last 24 months carry the second most weight. This is why it is crucial to pay all bills on time
and do not miss any payments.</p>

    ";
        // line 463
        if ((($context["derogatory_accounts_total"] ?? null) > 0)) {
            // line 464
            echo "
        <table class=\"size-100\">
            <thead>
                <tr>
                    <th>
                        Creditor/Furnisher
                    </th>
                    <th>
                        Equifax
                    </th>
                    <th>
                        Experian
                    </th>
                    <th>
                        TransUnion
                    </th>
                    <th>
                        Issue
                    </th>
                <tr/>
            </thead>
            <tbody>

                ";
            // line 487
            $context["x"] = 1;
            // line 488
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["derogatory_accounts"] ?? null));
            foreach ($context['_seq'] as $context["misc"] => $context["derogatory_items"]) {
                // line 489
                echo "                ";
                if (((($context["x"] ?? null) % 2) == 0)) {
                    // line 490
                    echo "                    ";
                    $context["xclass"] = "class=odd-color";
                    // line 491
                    echo "                ";
                } else {
                    // line 492
                    echo "                    ";
                    $context["xclass"] = "";
                    // line 493
                    echo "                ";
                }
                // line 494
                echo "                <tr ";
                echo twig_escape_filter($this->env, ($context["xclass"] ?? null), "html", null, true);
                echo ">
                    <td>";
                // line 495
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["derogatory_items"], "account", [], "any", false, false, false, 495), "html", null, true);
                echo "</td>
                    <td>";
                // line 496
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["derogatory_items"], "equifax_account_date", [], "any", false, false, false, 496), "html", null, true);
                echo "</td>
                    <td>";
                // line 497
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["derogatory_items"], "experian_account_date", [], "any", false, false, false, 497), "html", null, true);
                echo "</td>
                    <td>";
                // line 498
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["derogatory_items"], "trans_union_account_date", [], "any", false, false, false, 498), "html", null, true);
                echo "</td>
                    <td>";
                // line 499
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["derogatory_items"], "unique_status", [], "any", false, false, false, 499), "html", null, true);
                echo "</td>
                </tr>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['misc'], $context['derogatory_items'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 502
            echo "            </tbody>
        </table>
    ";
        }
        // line 505
        echo "
    <h4>Your Public Records:</h4>

    <p>
        You have <strong>";
        // line 509
        echo twig_escape_filter($this->env, ($context["public_records_total"] ?? null), "html", null, true);
        echo "</strong> public records. Public records include details of court records, bankruptcy filings, tax liens and monetary judgments. These generally remain on your Credit Report for 7 to 10 years
    </p>

    ";
        // line 512
        if ((($context["public_records_total"] ?? null) > 0)) {
            // line 513
            echo "        <table class=\"size-100\">
            <thead>
                <tr>
                    <th>Creditor/Furnisher</th>
                    <th>Equifax</th>
                    <th>Experian</th>
                    <th>TransUnion</th>
                    <th>Issue</th>
                </tr>
            </thead>
            <tbody>
            ";
            // line 524
            $context["x"] = 0;
            // line 525
            echo "            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["public_records"] ?? null));
            foreach ($context['_seq'] as $context["b"] => $context["public_records_accounts"]) {
                // line 526
                echo "                ";
                if (((($context["x"] ?? null) % 2) == 0)) {
                    // line 527
                    echo "                    ";
                    $context["xclass"] = "class=odd-color";
                    // line 528
                    echo "                ";
                } else {
                    // line 529
                    echo "                    ";
                    $context["xclass"] = "";
                    // line 530
                    echo "                ";
                }
                // line 531
                echo "                <tr ";
                echo twig_escape_filter($this->env, ($context["xclass"] ?? null), "html", null, true);
                echo ">
                ";
                // line 532
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["public_records_accounts"]);
                foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                    // line 533
                    echo "                    <td>";
                    echo twig_escape_filter($this->env, $context["value"], "html", null, true);
                    echo "</td>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 535
                echo "                </tr>
            ";
                // line 536
                $context["x"] = (($context["x"] ?? null) + 1);
                // line 537
                echo "            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['b'], $context['public_records_accounts'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 538
            echo "            </tbody>
        </table>
    ";
        }
        // line 541
        echo "
    <h4>Your Inquiries:</h4>

    <p>
        You have <strong>";
        // line 545
        echo twig_escape_filter($this->env, ($context["inquiry_total"] ?? null), "html", null, true);
        echo "</strong> inquiries on your reports. Each time you apply for credit it lowers your score. For that reason we ask during credit repair that you <u>do not apply for anything</u>.
    </p>

    ";
        // line 548
        if ((($context["inquiry_total"] ?? null) > 0)) {
            // line 549
            echo "
        <table class=\"size-100\">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Business Type</th>
                    <th>Date</th>
                    <th>Bureau</th>
                </tr>
            </thead>
            ";
            // line 559
            $context["x"] = 0;
            // line 560
            echo "            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["inquiry_accounts"] ?? null));
            foreach ($context['_seq'] as $context["a"] => $context["inquiry_items"]) {
                // line 561
                echo "                ";
                if (((($context["x"] ?? null) % 2) == 0)) {
                    // line 562
                    echo "                    ";
                    $context["xclass"] = "class=odd-color";
                    // line 563
                    echo "                ";
                } else {
                    // line 564
                    echo "                    ";
                    $context["xclass"] = "";
                    // line 565
                    echo "                ";
                }
                // line 566
                echo "                <tr ";
                echo twig_escape_filter($this->env, ($context["xclass"] ?? null), "html", null, true);
                echo ">
                ";
                // line 567
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["inquiry_items"]);
                foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                    // line 568
                    echo "                    <td>";
                    echo twig_escape_filter($this->env, $context["value"], "html", null, true);
                    echo "</td>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 570
                echo "                </td>
                ";
                // line 571
                $context["x"] = (($context["x"] ?? null) + 1);
                // line 572
                echo "            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['a'], $context['inquiry_items'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 573
            echo "        </table>

    ";
        }
        // line 576
        echo "
    <h5>We Are Experts In Disputing Errors On Your Report That Lower Your Score.</h5>
    <p class=\"page-break\">
        While we cannot promise to remove all of your negative items on your report, we do know how to use the law inyour favor and we have an awesome track record.
    </p>
    <h5>Credit Utilization:</h5>

    <table class=\"size-100\">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Transunion</th>
                <th>Equifax</th>
                <th>Experian</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Credit Card Limit</td>
                <td>\$";
        // line 595
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "trans_union_limit", [], "any", false, false, false, 595), "html", null, true);
        echo "</td>
                <td>\$";
        // line 596
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "equifax_limit", [], "any", false, false, false, 596), "html", null, true);
        echo "</td>
                <td>\$";
        // line 597
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "experian_limit", [], "any", false, false, false, 597), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Open Credit Card Debt</td>
                <td>\$";
        // line 601
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "trans_union_balance", [], "any", false, false, false, 601), "html", null, true);
        echo "</td>
                <td>\$";
        // line 602
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "equifax_balance", [], "any", false, false, false, 602), "html", null, true);
        echo "</td>
                <td>\$";
        // line 603
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "experian_balance", [], "any", false, false, false, 603), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>";
        // line 607
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "trans_union_percent", [], "any", false, false, false, 607), "html", null, true);
        echo "% <img src=\"images/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "trans_union_percent_img", [], "any", false, false, false, 607), "html", null, true);
        echo "\" /></td>
                <td>";
        // line 608
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "equifax_percent", [], "any", false, false, false, 608), "html", null, true);
        echo "% <img src=\"images/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "equifax_percent_img", [], "any", false, false, false, 608), "html", null, true);
        echo "\" /></td>
                <td>";
        // line 609
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "experian_percent", [], "any", false, false, false, 609), "html", null, true);
        echo "% <img src=\"images/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "experian_percent_img", [], "any", false, false, false, 609), "html", null, true);
        echo "\" /></td>
            </tr>
        </tbody>
    </table>

    <p>
        You have <strong>\$";
        // line 615
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "total_limit", [], "any", false, false, false, 615), "html", null, true);
        echo "</strong> in revolving credit lines and your balances average at <strong>\$";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "total_balance", [], "any", false, false, false, 615), "html", null, true);
        echo "</strong> which means that you are utilizing <strong>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["credit_info"] ?? null), "total_percent", [], "any", false, false, false, 615), "html", null, true);
        echo "%</strong> of
your available credit line.
    </p>

    <h5>How this impacts your score</h5>

    <p>
        The purpose of a credit score is for lenders to determine the likelihood that you will repay money you borrow. Therefore, the scoring algorithms looks to see that you're not overextended in credit card debt and living beyond your means. As a rule of thumb, maxing out your cards will lower your score while showing more available credit will increase your score.
    </p>

    <p>
        Pro tip: If you're carring high balances, a quick trick to increase to score is to pay your balances down to below 25% of the available credit limit of each card and never spend any more than that, even if you pay the bill off in full each month.
    </p>

    <h5>How quickly will I see the changes?</h5>

    <p class=\"page-break\">
        Credit card companies report your balances to the bureaus once per month (each on a different day) and your credit reports and scores at the credit monitoring company will only update once per month, therefore it can take upto two months to see this begin to impact your scores. For this reason, it's important to understand that improving your credit scores takes time and a commitment to change your daily habits and the way you spent money. The good news is that the sooner you can do it, the sooner your scores will rise.
    </p>

    <h5>Credit Utilization Summary:</h5>

    <table class=\"size-100\">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>EQUIFAX</th>
                <th>EXPERIAN</th>
                <th>TRANSUNION</th>
            </tr>
        </thead>
        <tbody>
            <tr class=\"odd-color\">
                <td><strong>Credit Summary</strong></td>
                <td colspan=\"3\">
                    An overview credit status including open and closed accounts and balance information
                </td>
            </tr>
            <tr>
                <td>Total Accounts:</td>
                <td>";
        // line 655
        echo twig_escape_filter($this->env, ($context["equifax_total_accounts"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 656
        echo twig_escape_filter($this->env, ($context["experian_total_accounts"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 657
        echo twig_escape_filter($this->env, ($context["transunion_total_accounts"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr class=\"odd-color\">
                <td>Open Accounts:</td>
                <td>";
        // line 661
        echo twig_escape_filter($this->env, ($context["equifax_open_accounts"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 662
        echo twig_escape_filter($this->env, ($context["experian_open_accounts"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 663
        echo twig_escape_filter($this->env, ($context["transunion_open_accounts"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Closed Accounts:</td>
                <td>";
        // line 667
        echo twig_escape_filter($this->env, ($context["equifax_closed_accounts"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 668
        echo twig_escape_filter($this->env, ($context["experian_closed_accounts"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 669
        echo twig_escape_filter($this->env, ($context["transunion_closed_accounts"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr class=\"odd-color\">
                <td>Balances:</td>
                <td>";
        // line 673
        echo twig_escape_filter($this->env, ($context["equifax_balances"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 674
        echo twig_escape_filter($this->env, ($context["experian_balances"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 675
        echo twig_escape_filter($this->env, ($context["transunion_balances"] ?? null), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Payments:</td>
                <td>";
        // line 679
        echo twig_escape_filter($this->env, ($context["equifax_payments"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 680
        echo twig_escape_filter($this->env, ($context["experian_payments"] ?? null), "html", null, true);
        echo "</td>
                <td>";
        // line 681
        echo twig_escape_filter($this->env, ($context["transunion_payments"] ?? null), "html", null, true);
        echo "</td>
            </tr>
        </tbody>
    </table>

    <h4>PART 4 - AN OVERVIEW OF OUR PROCESS</h4>

    <h5>Our Plan of Action</h5>

    <p>The credit system is flawed, and nearly 80% of all reports have errors that can lower your score. But you have rights and we know how to use them to your benefit! The law gives you the right to dispute any item on your credit reports. And if those items cannot be verified, they must be removed. So we will write many letters to the bureaus. If they can't prove it, they must remove it! And we are very good at this!</p>

    <h5>We Provide Document Preparation And Credit Education</h5>

    <p class=\"page-break\">
        We will be drafting many letters on your behalf to credit bureaus and creditors, to check for compliance and challenge the items you wish us to challenge. Along the way, we will also guide you how to better manage your credit -- and how to keep your awesome credit long after our work is done.
    </p>

    <h4>PART 5 - YOUR PART IN THE PROCESS</h4>

    <h5>Your Next Steps</h5>

    <ol>
        <li>Log Into Your Client Portal. We will email you the login details.</li>
        <li>. Provide a copy of your Photo ID, Social Security Card and a copy of the top section of a recent utility bill (or an insurance statement or some other bill) as proof of your current address to include with our letters to the credit bureaus. You can scan the documents or take a clear picture of these on your mobile device placing them on a white plain paper and upload them to us in your client portal.</li>
    </ol>

    <h5>How You Can Speed Up The Process</h5>

    <ol>
        <li>Stop applying for credit (Each time you do it lowers your scores.)</li>
        <li>Do not close any accounts (This also lowers your score.)</li>
        <li>Pay your credit cards down to below 25% of the available credit line. This will make a huge positive impact on your credit score.</li>
        <li>Never spend more than 25% of the available credit line, even if you pay the balance off in full each month.</li>
        <li>Pay your bills on time! One missed payment will lower your score dramatically and undo all the work we are doing.</li>
        <li>Keep your credit monitoring account active throughout the credit repair process, so we can see the changes to your accounts and scores. Your score won't suffer if you're ordering your own reports. Besure to let us know your login details to the credit monitoring account. You can add those in your client portal.</li>
        <li>Most importantly, We'll be sending many letters to the bureaus. Be sure to open all of your mail and forward the replies here to us. This can be as simple as taking a photo with your phone and uploading it to your portal (or attaching to an email).</li>
    </ol>

    <h5>This Process Takes Time</h5>

    <p>Remember, it has taken you years to get your credit into its current state, so cleaning it up will not happen overnight. We cannot dispute everything all at once, or the credit bureaus will reject the disputes by marking them as \"frivolous,\" so we must do this carefully and strategically. It takes 30 to 45 days for bureaus and creditors to respond to each letter, and even more time for changes to reflect on your reports. A difficult item may take multiple letters to multiple parties, so patience is key. Thanks to technology (and by logging into our client portal), you'll receive real-time updates of the work we're doing every step of the way.</p>

    <p>By following our program and our advice, your credit will improve -- and along the way, we'll teach you how to maintain your excellent credit long after our work is done.</p>

    <h5>So Let's Get Started!</h5>

    <p>How do we do that? Just reach out to us, so we can complete your signup process and activate your client portal access (if we haven't already).</p>

    <p>
        Our phone number is (800) 478-7119</br>
Our email is info@toptierfinancialsolutions.com</br>
Our site is at https://www.toptierfinancialsolutions.com/</br>
    </p>

    <p>
        Throughout this process, our contact information is always on our website and in our emails. You can also send us by secure messages in your portal. We want to hear from you and we are eager to help. Once the credit repair process has begun we will also be sending you progress reports and updates every step of the way. 
    </p>

    <p>
        We appreciate that you choose us. We look forward to working with you to improve your credit and your financial future!
    </p>

    <p>
        Credit is our passion. We understand how important your credit is for your future and we we work tirelessly to make sure we are able to help you achieve your financial goals.
    </p>
</div>
";
    }

    public function getTemplateName()
    {
        return "credit-report.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1012 => 681,  1008 => 680,  1004 => 679,  997 => 675,  993 => 674,  989 => 673,  982 => 669,  978 => 668,  974 => 667,  967 => 663,  963 => 662,  959 => 661,  952 => 657,  948 => 656,  944 => 655,  897 => 615,  886 => 609,  880 => 608,  874 => 607,  867 => 603,  863 => 602,  859 => 601,  852 => 597,  848 => 596,  844 => 595,  823 => 576,  818 => 573,  812 => 572,  810 => 571,  807 => 570,  798 => 568,  794 => 567,  789 => 566,  786 => 565,  783 => 564,  780 => 563,  777 => 562,  774 => 561,  769 => 560,  767 => 559,  755 => 549,  753 => 548,  747 => 545,  741 => 541,  736 => 538,  730 => 537,  728 => 536,  725 => 535,  716 => 533,  712 => 532,  707 => 531,  704 => 530,  701 => 529,  698 => 528,  695 => 527,  692 => 526,  687 => 525,  685 => 524,  672 => 513,  670 => 512,  664 => 509,  658 => 505,  653 => 502,  644 => 499,  640 => 498,  636 => 497,  632 => 496,  628 => 495,  623 => 494,  620 => 493,  617 => 492,  614 => 491,  611 => 490,  608 => 489,  603 => 488,  601 => 487,  576 => 464,  574 => 463,  566 => 458,  552 => 447,  548 => 446,  544 => 445,  537 => 441,  533 => 440,  529 => 439,  522 => 435,  518 => 434,  514 => 433,  507 => 429,  503 => 428,  499 => 427,  492 => 423,  488 => 422,  484 => 421,  453 => 393,  447 => 390,  441 => 387,  180 => 131,  133 => 89,  116 => 75,  109 => 73,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "credit-report.html.twig", "/home/web.user/app/manage.toptierfinancial.com/frantz-chery/templates/credit-report.html.twig");
    }
}
