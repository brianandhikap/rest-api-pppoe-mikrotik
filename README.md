## Rest API Mikrotik
Work tested on v 7.XX

## Tutorial
1. Clone repository:
   ```bash
   git clone https://github.com/brianandhikap/rest-api-pppoe-mikrotik

2. Edit File 
   ```bash
   $host 
   $user
   $pass

3. Basic (Maybe Will be update soon)
   ```bash
   pppoe_active.php (For List PPPoE Active)
   pppoe_secret.php (For List PPPoE Secret)
   pppoe_total.php (For Total PPPoE Secret and Active Client PPPoe)

3. For Backend (still under Development)
   ```bash
   action.php?add_profile=Paket50M|local=Pool-PPPoE-nextbiy.my.id|remote=Pool-PPPoE-nextbiy.my.id|50M/50M (Add Profile)
   action.php?remove_profile=Paket20M (Remove Profile)
   action.php?disable_secret=Brian@nextbiy.my.id (Disable Secret Brian@nextbiy.my.id)
   action.php?enable_secret=Brian@nextbiy.my.id (Enable Secret Brian@nextbiy.my.id)
   action.php?add_secret=Brianandhikap@nextbiy.my.id|password=brianandhikap|service=pppoe|profile=Paket20M (Enable Secret Brianandhikap@nextbiy.my.id)

### Star repo is FREE...
