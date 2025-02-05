
// const { MongoClient, ServerApiVersion } = require('mongodb');
// const uri = "mongodb+srv://2021kucp1005:Barman@2002@cluster0.fkzm4.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

// // Create a MongoClient with a MongoClientOptions object to set the Stable API version
// const client = new MongoClient(uri, {
//   serverApi: {
//     version: ServerApiVersion.v1,
//     strict: true,
//     deprecationErrors: true,
//   }
// });

// async function run() {
//   try {
//     // Connect the client to the server	(optional starting in v4.7)
//     await client.connect();
//     // Send a ping to confirm a successful connection
//     await client.db("admin").command({ ping: 1 });
//     console.log("Pinged your deployment. You successfully connected to MongoDB!");
//   } finally {
//     // Ensures that the client will close when you finish/error
//     await client.close();
//   }
// }
// run().catch(console.dir);
const express = require('express');
const nodemailer = require('nodemailer');
const cors = require('cors');
const bodyParser = require('body-parser');
require('dotenv').config();

const app = express();
const PORT = 5000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Nodemailer Transporter
const transporter = nodemailer.createTransport({
  service: 'gmail',
  auth: {
    user: 'barmanabheek@gmail.com',  // Your Gmail ID
    pass: 'dkgn fnkt iwzy psox',    // Your App Password (not your normal password)
  },
});

// API Route to send emails
app.post('/send-message', async (req, res) => {
  try {
    const { name, email, message } = req.body;

    // Validate data
    if (!name || !email || !message) {
      console.log('Validation Failed: Missing fields');
      return res.status(400).json({ error: 'All fields are required' });
    }

    // Email options
    const mailOptions = {
      from: {email}, // Sender email from form input
      to: 'barmanabheek@gmail.com', // Your email to receive messages
      subject: `New Message from ${name} (from your portfolio website)`,
      text: `Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}`,
    };

    // Send email
    const info = await transporter.sendMail(mailOptions);
    console.log('Email sent:', info.response);
    
    res.status(200).json({ message: 'Email sent successfully' });

  } catch (error) {
    console.error('Error occurred:', error);
    res.status(500).json({ error: 'Failed to send email' });
  }
});

// Start the server
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
