## GoodSign CLI documentation
GoodSign CLI client for quickly getting up and running with the GoodSign eSignatue API.

This CLI client can register a new account and send a document off for signing in about 2 minutes! 

### What the API client does
We've made things simple. 

#### Send an templated document
1. Register a new account all within the terminal (or login)
2. Automatically create a `template` document that you can trigger for signing.
3. Trigger a `CURL` request with details about the signers from the command line.

#### Upload and send a PDF document with markup
You can also get started with our PDF API.
This API allows you to upload a PDF with special `text tags` contained in the document
that tells us where the signing or input fields should be automatically placed.
eg sign here field  `[sign|signer1]`
or a enter text     `[input|signer| Your Phone Number ]`

1. Client will download our quick start PDF with some text tags
2. Configure the signer (default is you)
3. Generate a working curl command to upload this PDF and send it to a signer. 

#### Other Commands
- send a reminder to a sender
- void a document 
- send a webhook (coming soon) 

### How to contribute.
I'm happy to receive pull requests from your fork. 
Get in touch via using john then_the_at_symbol goodsign.io if there is something you want to add. 

I'm trying to keep the GoodSign CLI client simple – so new users can figure out the API pretty quicly. 

There might be an option to build an advanced version of this client
which includes everything. 

### Installing the source code (for editing)
Clone repository into a new folder and run the composer command below. 
This tool is built on https://laravel-zero.com 
All the source code is PHP. 

### install dependencies
`composer install`

### Smaller build Sizes 
run this command
`composer install --no-dev`


