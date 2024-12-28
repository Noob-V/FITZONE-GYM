document.getElementById("submit-comment").addEventListener("click", function () {
    const commentInput = document.getElementById("comment-input");
    const commentText = commentInput.value.trim();

    if (commentText) {
        const commentList = document.querySelector(".comment-list");
        const commentDiv = document.createElement("div");
        commentDiv.classList.add("comment");
        
        commentDiv.innerHTML = `
            <p class="comment-text">${commentText}</p>
            <div class="comment-actions">
                <button class="like-btn"><i class='bx bx-thumbs-up'></i> Like</button>
                <button class="dislike-btn"><i class='bx bx-thumbs-down'></i> Dislike</button>
                <button class="reply-btn">Reply</button>
            </div>
            <div class="reply-input" style="display:none;">
                <textarea class="reply-textarea" placeholder="Write your reply..." rows="2"></textarea>
                <button class="submit-reply btn">Post Reply</button>
            </div>
            <div class="reply-list"></div>
        `;

        commentList.appendChild(commentDiv);
        commentInput.value = ""; 

        
        const replyButton = commentDiv.querySelector(".reply-btn");
        replyButton.addEventListener("click", function () {
            const replyInput = commentDiv.querySelector(".reply-input");
            replyInput.style.display = replyInput.style.display === "none" ? "block" : "none";
        });

        const submitReplyButton = commentDiv.querySelector(".submit-reply");
        submitReplyButton.addEventListener("click", function () {
            const replyTextarea = commentDiv.querySelector(".reply-textarea");
            const replyText = replyTextarea.value.trim();

            if (replyText) {
                const replyDiv = document.createElement("div");
                replyDiv.classList.add("reply");
                replyDiv.innerHTML = `
                    <p class="reply-text">${replyText}</p>
                    <div class="reply-actions">
                        <button class="like-btn"><i class='bx bx-thumbs-up'></i> Like</button>
                        <button class="dislike-btn"><i class='bx bx-thumbs-down'></i> Dislike</button>
                    </div>
                `;
                commentDiv.querySelector(".reply-list").appendChild(replyDiv);
                replyTextarea.value = ""; 
                replyInput.style.display = "none"; 
            }
        });
    }
});

