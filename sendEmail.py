import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
import sys

def sendEmail(itemName, retailers, prices, urls, senderEmail, destEmail):

    try:
        s = smtplib.SMTP(senderEmail[2])
        s.starttls()
        s.login(senderEmail[0], senderEmail[1])
    except smtplib.SMTPAuthenticationError:
        print('Failed to login')
    else:
        print('Logged in! Composing message..')
        msg = MIMEMultipart('alternative')
        msg['Subject'] = "Price Alert Notification"
        msg['From'] = senderEmail[0]
        msg['To'] = destEmail

        msgText = "We have detected that the price of one of your items has dropped below your Price Alert threshold on one or more sites!\n\nItem Name: " + itemName + "\n\n"

        for idx in range(0, len(retailers)):
            msgText += "Site: " + retailers[idx] + "\nPrice: " + prices[idx] + "\nURL: " + urls[idx] + "\n\n"

        msgText += "Thanks for using Price Alert!"
        part = MIMEText(msgText, 'plain')
        msg.attach(part)
        s.sendmail(senderEmail[0], destEmail, msg.as_string())
        print('Message has been sent.')

def main():
    sendEmail(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5], sys.argv[6])
main()