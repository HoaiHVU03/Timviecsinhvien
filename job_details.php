                    <div class="card-body">
                        <h4 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h4>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                        
                        <div class="mb-3">
                            <h5>Mô tả công việc</h5>
                            <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <h5>Yêu cầu</h5>
                            <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Mức lương:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                            <div class="d-grid gap-2">
                                <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">Ứng tuyển</a>
                            </div>
                        <?php endif; ?>
                    </div> 