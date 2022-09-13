import websocket
import _thread
import time
import rel
import json
import atexit
import signal
import os
import glob



def handle_signal(*args):
    print("Termination request received.")
    exit()
    
def cleaup_at_exit():
    ws = websocket.create_connection("ws://192.168.8.1:7681/")
    ws.send('{"CloseRfPower":{}}')
    ws.close()
    if os.path.exists("SESSION_ID"):
        os.remove("SESSION_ID")
    print("\r\nExit")
    

atexit.register(cleaup_at_exit)
signal.signal(signal.SIGTERM, handle_signal)
signal.signal(signal.SIGINT, handle_signal)
DATAPOOL = {}
SESSION_ID = ""

def create_csv(DATA, SID):
    CSV=""
    for EPC in DATA:
        CSV=CSV+EPC
        T=DATA[EPC]["T"]
        # print(T)
        for n in range(1, len(T)-1):  
            CSV=CSV+','+str(DATA[EPC]["T"][n])
            # print(DATA[EPC]["T"][0])
        CSV=CSV+"\r\n"
    print("\r\n["+SID+"]:\r\n"+CSV)
    os.remove("SESSION_ID")   
    with open('./data/'+SID+'.csv', 'w+') as file:
            file.write(CSV)          
    

def on_message(ws, message):
    global DATAPOOL,SESSION_ID
    # print(message)
    TAG = json.loads(message)
    if "CloseRfPower_AckOk" in TAG:
        if os.path.exists("SESSION_ID"):
            print("### Scan Stopped ###")
            if DATAPOOL!={}:
                print(DATAPOOL)
                create_csv(DATAPOOL, SESSION_ID)
                DATAPOOL.clear()
        return
    if "GenRead_AckTypeOk" not in TAG:
        return 
    elif len(DATAPOOL) == 0:
        files = glob.glob('./data/*')
        for f in files:
            os.remove(f)     
        SESSION_ID=str(int(time.time()))
        with open('SESSION_ID', 'w+') as file:
            file.write(SESSION_ID+'\n')        
        print("### Scan Started ###  ["+SESSION_ID+"]")
    EPC = TAG["GenRead_AckTypeOk"]["EPC"]
    # print(EPC)
    T1 = time.time()
    if EPC not in DATAPOOL:
        DATAPOOL[EPC]={}
        DATAPOOL[EPC]["T"]=[]
        DATAPOOL[EPC]["T"].append(0)
        DATAPOOL[EPC]["L"]=int(T1)
    else:
        Td = T1-DATAPOOL[EPC]["L"]
        if Td>=3:
            DATAPOOL[EPC]["T"].append(round(Td,2))
            DATAPOOL[EPC]["L"]=int(T1)
    # print(EPC)
    # print(DATAPOOL) 
            
def on_error(ws, error):
    print(error)

def on_close(ws, close_status_code, close_msg):
    print("### Scan Interrupted ###")

def on_open(ws):
    print("### Connection Opened ###")
    # ws.send('{"GenRead":{"Antennas":"00000001","Q":1,"OpType":0,"LenTid":0,"PointerUserEvb":0,"LenUser":0}}')
    

if __name__ == "__main__":
    websocket.enableTrace(False)
    ws = websocket.WebSocketApp("ws://192.168.8.1:7681",
                              on_open=on_open,
                              on_message=on_message,
                              on_error=on_error,
                              on_close=on_close)

    ws.run_forever(dispatcher=rel)  # Set dispatcher to automatic reconnection
    rel.signal(2, rel.abort)  # Keyboard Interrupt
    rel.dispatch()