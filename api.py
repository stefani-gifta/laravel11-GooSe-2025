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
    data = request.json
    user_input = data.get('message', '')
    one_line_mode = data.get('oneLineMode', False)

    # --- DEBUGGING HELPER (CHECK THIS) ---
    print(f"\n[INPUT] User message: {user_input} {one_line_mode}") 
    # ------------------------------------

    if not user_input:
        return jsonify({"reply": "Sorry, I didn't receive any message."})

    # Full explanation
    full_prompt = f"Explain this AI concept with an analogy: {user_input}"
    inputs = tokenizer(full_prompt, return_tensors="pt", truncation=True, max_length=256)
    output_ids = model.generate(
        **inputs,
        max_new_tokens=128,
        num_beams=5,
        temperature=0.7,
        early_stopping=True
    )
    full_reply = tokenizer.decode(output_ids[0], skip_special_tokens=True)

    # One-line summary (optional)
    one_line_reply = None
    if one_line_mode:
        summary_prompt = f"Summarize this in ONE short sentence: {full_reply}"
        summary_inputs = tokenizer(summary_prompt, return_tensors="pt", truncation=True, max_length=64)
        summary_output_ids = model.generate(
            **summary_inputs,
            max_new_tokens=32,
            num_beams=5,
            temperature=0.7,
            early_stopping=True
        )
        one_line_reply = tokenizer.decode(summary_output_ids[0], skip_special_tokens=True)

    # --- DEBUGGING HELPER (CHECK THIS) ---
    print(f"[OUTPUT] AI response: {full_reply}")
    print(f"[OUTPUT] AI one line response: {one_line_reply}\n")
    # ------------------------------------

    return jsonify({
        "full_reply": full_reply,
        "one_line_reply": one_line_reply
    })

if __name__ == '__main__':
    # Run on port 5000
    app.run(host='127.0.0.1', port=5000, debug=True)