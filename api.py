from flask import Flask, request, jsonify
from transformers import AutoTokenizer, AutoModelForSeq2SeqLM
import torch

app = Flask(__name__)

# --- CONFIGURATION ---
MODEL_DIR = "flan-t5-ai-simple-bot" 

print("Loading AI model... Please wait...")

# Check if the model folder exists; if not, use Google's default model
try:
    tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
    model = AutoModelForSeq2SeqLM.from_pretrained(MODEL_DIR)
    print("Trained model successfully loaded!")
except:
    print(f"Folder {MODEL_DIR} not found. Using default model 'google/flan-t5-base'")
    MODEL_DIR = "google/flan-t5-base"
    tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
    model = AutoModelForSeq2SeqLM.from_pretrained(MODEL_DIR)

@app.route('/predict', methods=['POST'])
def predict():
    # 1. Receive data
    data = request.json
    user_input = data.get('message', '')
    
    # --- DEBUGGING HELPER (CHECK THIS) ---
    print(f"\n[INPUT] User message: {user_input}") 
    # ------------------------------------

    if not user_input:
        return jsonify({"reply": "Sorry, I didn't receive any message."})

    # 2. AI processing
    prompt = f"Explain this AI concept with an analogy: {user_input}"
    
    inputs = tokenizer(
        prompt,
        return_tensors="pt",
        truncation=True,
        max_length=256
    )

    output_ids = model.generate(
        **inputs,
        max_new_tokens=128,
        num_beams=5,
        temperature=0.7,
        early_stopping=True
    )

    bot_reply = tokenizer.decode(output_ids[0], skip_special_tokens=True)

    # --- DEBUGGING HELPER (CHECK THIS) ---
    print(f"[OUTPUT] AI response: {bot_reply}\n")
    # ------------------------------------
    
    return jsonify({"reply": bot_reply})

if __name__ == '__main__':
    # Run on port 5000
    app.run(host='127.0.0.1', port=5000, debug=True)