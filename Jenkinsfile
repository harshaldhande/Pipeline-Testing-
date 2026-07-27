[luffy@jenkins projects]$ cat Jenkinsfile
pipeline {

    agent any

    environment {
        IMAGE_NAME      = "dockerforlearning0213/todo-app"
        IMAGE_TAG       = "${BUILD_NUMBER}"
        NAMESPACE       = "todo-app"
        DEPLOYMENT      = "todo-app"
        CONTAINER       = "todo-app"

        SONAR_SCANNER   = tool 'SonarScanner'
        SONAR_PROJECT   = "todo-app"
    }

    stages {

        stage('Checkout Source') {
            steps {
                echo "========== Checking Out Source =========="
                checkout scm
            }
        }

        stage('SonarQube Analysis') {
            steps {

                echo "========== SonarQube Analysis =========="

                withSonarQubeEnv('SonarQube') {

                    sh """
                        ${SONAR_SCANNER}/bin/sonar-scanner \
                        -Dsonar.projectKey=${SONAR_PROJECT} \
                        -Dsonar.projectName=Todo-App \
                        -Dsonar.sources=. \
                        -Dsonar.sourceEncoding=UTF-8
                    """
                }
            }
        }

        stage('Quality Gate') {
            steps {

                echo "========== Waiting For Quality Gate =========="

                timeout(time: 5, unit: 'MINUTES') {

                    waitForQualityGate abortPipeline: true

                }

                echo "========== Quality Gate Passed =========="
            }
        }

        stage('Build Docker Image') {
            steps {
                sh '''
                    echo "========== Building Docker Image =========="

                    docker build -t ${IMAGE_NAME}:${IMAGE_TAG} .

                    echo ""
                    echo "========== Docker Images =========="
                    docker images | grep todo-app
                '''
            }
        }



stage('Trivy Filesystem Scan') {
    steps {

        echo "========== Trivy Filesystem Scan =========="

        sh '''
            mkdir -p reports

            echo "Generating Filesystem Report..."

            trivy fs \
                --scanners vuln \
                --ignore-unfixed \
                --severity HIGH,CRITICAL \
                --format template \
                --template "@$HOME/.trivy/templates/html.tpl" \
                --output reports/trivy-fs-report.html \
                .

            echo "Scanning Filesystem..."

            trivy fs \
                --scanners vuln \
                --ignore-unfixed \
                --severity HIGH,CRITICAL \
                .
        '''
    }
}

stage('Trivy Docker Image Scan') {
    steps {

        echo "========== Trivy Docker Image Scan =========="

        sh '''
            echo "Generating Image Report..."

            trivy image \
                --scanners vuln \
                --ignore-unfixed \
                --severity HIGH,CRITICAL \
                --format template \
                --template "@$HOME/.trivy/templates/html.tpl" \
                --output reports/trivy-image-report.html \
                ${IMAGE_NAME}:${IMAGE_TAG}

            echo "Scanning Docker Image..."

            trivy image \
                --scanners vuln \
                --ignore-unfixed \
                --severity HIGH,CRITICAL \
                ${IMAGE_NAME}:${IMAGE_TAG}
        '''
    }
}



        stage('Push Docker Image') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub',
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_PASS'
                    )
                ]) {

                    sh '''
                        echo "========== Docker Login =========="

                        echo "$DOCKER_PASS" | docker login \
                        -u "$DOCKER_USER" \
                        --password-stdin

                        echo ""
                        echo "========== Pushing Docker Image =========="

                        docker push ${IMAGE_NAME}:${IMAGE_TAG}

                        docker logout
                    '''
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                withCredentials([
                    file(credentialsId: 'kubeconfig', variable: 'KUBECONFIG')
                ]) {

                    script {

                        try {

                            echo "========== Deploying Image =========="

                            sh """
                                kubectl set image deployment/${DEPLOYMENT} \
                                ${CONTAINER}=${IMAGE_NAME}:${IMAGE_TAG} \
                                -n ${NAMESPACE}
                            """

                            echo "========== Waiting for Rollout =========="

                            sh """
                                kubectl rollout status deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE} \
                                --timeout=120s
                            """

                            echo "========== Rollout History =========="

                            sh """
                                kubectl rollout history deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE}
                            """

                            echo "========== Deployment Successful =========="

                        } catch (err) {

                            echo "========== Deployment Failed =========="
                            echo "${err}"

                            echo "========== Rolling Back =========="

                            sh """
                                kubectl rollout undo deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE}
                            """

                            echo "========== Waiting for Rollback =========="

                            sh """
                                kubectl rollout status deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE}
                                --timeout=120s
                            """

                            error("Deployment failed. Rollback completed successfully.")
                        }
                    }
                }
            }
        }

        stage('Health Check') {
            steps {
                withCredentials([
                    file(credentialsId: 'kubeconfig', variable: 'KUBECONFIG')
                ]) {

                    script {

                        echo "========== Health Check =========="

                        sh """
                            kubectl get pods -n ${NAMESPACE}
                        """

                        timeout(time: 2, unit: 'MINUTES') {

                            sh """
                                kubectl wait \
                                --for=condition=Ready \
                                pod \
                                -l app=${DEPLOYMENT} \
                                -n ${NAMESPACE} \
                                --timeout=120s
                            """
                        }

                        echo "========== Pod Details =========="

                        sh """
                            kubectl get pods \
                            -n ${NAMESPACE} \
                            -o wide
                        """

                        echo "========== All Pods are Healthy =========="
                    }
                }
            }
        }
    }

    post {

        success {

            echo "========================================="
            echo "Deployment Successful"
            echo "Image      : ${IMAGE_NAME}:${IMAGE_TAG}"
            echo "Namespace  : ${NAMESPACE}"
            echo "========================================="
        }

        failure {

            echo "========================================="
            echo "Deployment Failed"
            echo "Rollback Executed (if previous revision existed)"
            echo "========================================="
        }

                always {

                        echo "========== Archiving Trivy Reports =========="
						
						publishHTML(target: [
							allowMissing: false,
							alwaysLinkToLastBuild: true,
							keepAll: true,
							reportDir: 'reports',
							reportFiles: 'trivy-image-report.html',
							reportName: 'Trivy Image Report'
						])

						publishHTML(target: [
							allowMissing: false,
							alwaysLinkToLastBuild: true,
							keepAll: true,
							reportDir: 'reports',
							reportFiles: 'trivy-fs-report.html',
							reportName: 'Trivy Filesystem Report'
						])

                        archiveArtifacts artifacts: 'reports/**/*.html', fingerprint: true

                        echo "========== Cleaning Docker Images =========="

                        sh '''
                                docker image prune -f
                        '''

                        echo "========== Pipeline Finished =========="
                }
    }
}




